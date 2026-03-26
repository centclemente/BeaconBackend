<?php


namespace App\Imports;

use App\Models\Team;
use App\Models\Systems;
use App\Models\Category;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class Import implements ToCollection, WithHeadingRow, WithChunkReading
{
    use ApiResponse;
    use Importable;

    private $errors = [];
    private $duplicates = [];
    private $existingCategories = [];
    private $existingTeams = [];
    private $existingSystems = [];
    private $categories = [];
    private $teams = [];
    private $systems = [];

    public function __construct()
    {
        $this->existingCategories = Category::pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [Str::lower($name) => $id])
            ->toArray();

        $this->existingTeams = Team::pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [Str::lower($name) => $id])
            ->toArray();

        $this->existingSystems = Systems::pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [Str::lower($name) => $id])
            ->toArray();
    }
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception(json_encode([
                'message' => 'The uploaded file is empty.',
                'errors' => []
            ]));
        }

        $validRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            $data = $this->prepareRowData($row->toArray());

            if (empty($data['system_name']) && empty($data['description']) && empty($data['team_name'])) {
                continue;
            }

            $uniqueKey = Str::lower($data['system_name']) . '|' . $data['description'] . '|' . $data['raised_date'] . '|' . Str::lower($data['category']);
            if (in_array($uniqueKey, $this->duplicates)) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['duplicate' => ['This row is a duplicate']],
                    'values' => $data
                ];  
                continue;
            }
            $this->duplicates[] = $uniqueKey;

            if ($this->validateRow($data, $rowNumber)) {
                $validRows[] = $data;
            }
        }

        if (!empty($this->errors)) {
            throw new \Exception(json_encode([
                'message' => 'Validation: Failed to Upload File',
                'errors' => $this->errors
            ]));
        }

        DB::transaction(function () use ($validRows) {
            foreach ($validRows as $data) {
                $this->storeRow($data);
            }
        });
    }

    protected function validateRow($data, $rowNumber)
    {
        $validator = Validator::make($data, $this->rules(), $this->customValidationMessages());

        if (!empty($data['system_name'])) {
            $systemKey = Str::lower($data['system_name']);
            if (!isset($this->existingSystems[$systemKey])) {
                $validator->errors()->add('System Name', 'The system does not exist.');
            }
        }

        if (!empty($data['category'])) {
            $categoryKey = Str::lower($data['category']);
            if (!isset($this->existingCategories[$categoryKey])) {
                $validator->errors()->add('Category', 'The category does not exist.');
            }
        }

        if (!empty($data['team_name'])) {
            $teamKeys = $this->extractTeams($data['team_name']);
            $missingTeams = array_diff($teamKeys, array_keys($this->existingTeams));

            if (!empty($missingTeams)) {
                $validator->errors()->add('Team Name ', 'The following teams do not exist: ' . implode(', ', $missingTeams));
            }

            if (!empty($data['system_name'])) {
                $systemKey = Str::lower($data['system_name']);
                if (isset($this->existingSystems[$systemKey])) {
                    $systemId = $this->existingSystems[$systemKey];
                    $requestedTeamIds = array_values(array_intersect_key($this->existingTeams, array_flip($teamKeys)));
                    
                    $taggedTeamIds = Systems::find($systemId)
                        ->team()
                        ->pluck('team_id')
                        ->toArray();

                    $untaggedTeamIds = array_diff($requestedTeamIds, $taggedTeamIds);

                    if (!empty($untaggedTeamIds)) {
                        $untaggedTeamNames = Team::whereIn('id', $untaggedTeamIds)
                            ->pluck('name')
                            ->toArray();
                        $validator->errors()->add('Team Name', 'The system is not tagged to these teams: ' . implode(', ', $untaggedTeamNames));
                    }
                }
            }
        }

        if (!empty($data['system_name']) && !empty($data['description']) && !empty($data['raised_date'])) {
            $systemKey = Str::lower($data['system_name']);
            if (isset($this->existingSystems[$systemKey])) {
                $systemId = $this->existingSystems[$systemKey];
                $categoryId = $this->existingCategories[Str::lower($data['category'])] ?? null;
                $duplicate = Progress::where('system_id', $systemId)
                    ->where('category_id', $categoryId)    
                    ->where('description', $data['description'])
                    ->where('raised_date', $data['raised_date'])
                    ->exists();

                if ($duplicate) {
                    $validator->errors()->add('description', 'This progress record already exists for this system.');
                }
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            $this->errors[] = [
                'row' => $rowNumber,
                'errors' => $validator->errors()->toArray(),
                'values' => $data
            ];
            return false;
        }

        return true;
    }

    protected function storeRow($data)
    {
        $systemKey = Str::lower($data['system_name']);
        $categoryKey = Str::lower($data['category']);
        $teamKeys = $this->extractTeams($data['team_name']);

        $systemId = $this->existingSystems[$systemKey];
        $categoryId = $this->existingCategories[$categoryKey];
        $teamIds = array_values(array_intersect_key($this->existingTeams, array_flip($teamKeys)));

        $system = Systems::find($systemId);
        $system->team()->syncWithoutDetaching($teamIds);

        Progress::create([
            'description' => $data['description'],
            'raised_date' => $data['raised_date'],
            'target_date' => $data['target_date'],
            'status' => $data['status'] ?? 'pending',
            'remarks' => $data['remarks'],
            'category_id' => $categoryId,
            'system_id' => $systemId
        ]);
    }

    protected function extractTeams($teamString)
    {
        return collect(explode(',', $teamString))
            ->map(fn($team) => Str::lower(trim($team)))
            ->toArray();
    }

    public function prepareRowData($row)
    {
        return [
            'system_name' => $row['system_name'] ?? null,
            'team_name' => $row['team_name'] ?? null,
            'category' => $row['category'] ?? null,
            'description' => $row['description'] ?? null,
            'raised_date' => $this->transformDate($row['raised_date'] ?? null),
            'target_date' => $this->transformDate($row['target_date'] ?? null),
            'status' => !empty($row['status']) ? Str::lower($row['status']) : 'pending',
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
            'team_name' => 'required|string',
            'category' => 'required|max:255',
            'description' => 'required|max:500',
            'raised_date' => 'required|date',
            'target_date' => 'required|date',
            'status' => ['nullable', Rule::in(['pending', 'hold', 'done'])],
            'remarks' => 'nullable|string|max:1000',
        ];
    }
    public function customValidationMessages()
    {
        return [
            'system_name.required' => 'The system name field is required.',
            'team_name.required' => 'The team name field is required.',
            'category.required' => 'The category field is required.',
            'description.required' => 'The description field is required.',
            'raised_date.required' => 'The raised date field is required.',
            'target_date.required' => 'The target date field is required.',
            'status.in' => 'The status must be one of the following: pending, hold, done.',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}