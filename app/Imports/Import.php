<?php


namespace App\Imports;

use App\Models\Team;
use App\Models\Systems;
use App\Models\Category;
use App\Models\Progress;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;


class Import implements ToCollection, WithHeadingRow
{
    use ApiResponse;
    use Importable;
    
    private $ExistingCategories;
    private $ExistingTeams;

    private $errors = [];

    public function __construct()
    {
        $this->ExistingCategories = Category::pluck('name')->toArray();
        $this->ExistingTeams = Team::pluck('name')->toArray();

    }
    public function collection(Collection $rows)
    {

    if ($rows->isEmpty()) {

            throw new \Exception(json_encode([
                'message' => 'The uploaded file is empty.',
                'errors' => []
            ]));
        }
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            
            $data = $this->prepareRowData($row->toArray());
            
            $validator = Validator::make($data, $this->rules(), $this->customValidationMessages());
            
            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->toArray(),
                    'values' => $data
                ];
            }
        }


        if (!empty($this->errors)) {
            throw new \Exception(json_encode([
                'message' => 'Validation failed for multiple rows',
                'errors' => $this->errors
            ]));
        }

       
        foreach ($rows as $row) {
            $data = $this->prepareRowData($row->toArray());
            
           
            $team = Team::where('name', $data['team_name'])->first();
           $category = Category::where('name', $data['category'])->first();
           
            $system = Systems::firstOrCreate(
            ['name' => $data['system_name']],
            ['team_id' => $team->id]
        );
            
            Progress::create([
                "description" => $data['description'],
                "raised_date" => $data['raised_date'],
                "target_date" => $data['target_date'],
                "end_date" => $data['end_date'],
                "status" => $data['status'] ?? 'pending',
                "remarks" => $data['remarks'],
                "category_id" => $category->id,
                "system_id" => $system->id
            ]);
        }
    }

    protected function prepareRowData($row)
    {
        return [
            'system_name' => $row['system_name'] ?? null,
            'team_name' => $row['team'] ?? null,
            'category' => $row['category'] ?? null,
            'description' => $row['description'] ?? null,
            'raised_date' => $this->transformDate($row['raised_date'] ?? null),
            'target_date' => $this->transformDate($row['target_date'] ?? null),
            'end_date' => $this->transformDate($row['end_date'] ?? null),
            'status' => $row['status'] ?? 'pending',
            'remarks' => $row['remarks'] ?? null,
        ];
    }

    protected function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }
        return $value;
    }

    public function rules(): array
    {
        return [
            'system_name' => 'required|string|max:255',
            'category' => 'required|string|max:255|exists:category,name',
            'team_name' => 'required|string|max:255|exists:teams,name',
            'description' => [
                'required','string','max:500',Rule::unique('progress', 'description')
            ],
            'raised_date' => 'required|date',
            'target_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:raised_dates ',
            'status' => ['nullable', Rule::in(['pending', 'hold', 'done'])],
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category.exists' => 'The category does not exist.',
            'description.unique' => 'The Description already exist',
            'team_name.exists' => 'The Team does not exist.',
            'raised_date.date' => 'The Raised Date must be a valid date.',
            'target_date.date' => 'The Target Date must be a valid date.',
            'end_date.date' => 'The End Date must be a valid date.',
            'end_date.after_or_equal' => 'The End Date must be after or equal to the Raised Date.',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}