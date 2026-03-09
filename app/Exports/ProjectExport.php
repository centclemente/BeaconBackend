<?php

namespace App\Exports;

use App\Models\Progress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Progress::with(['category', 'systems']);

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['system_id'])) {
            $query->where('system_id', $this->filters['system_id']);
        }

        if (isset($this->filters['month'])) {
            $query->whereMonth('raised_date', $this->filters['month']);
        }

        if (isset($this->filters['year'])) {
            $query->whereYear('raised_date', $this->filters['year']);
        }


        return $query->orderBy('created_at', 'desc')->get();
    }

    public function map($progress): array
    {
        return [
            $progress->systems ? $progress->systems->name : 'N/A',
            $progress->category ? $progress->category->name : 'N/A',
            $progress->description ?? '',
            $progress->raised_date ?? '',
            $progress->start_date ?? '',
            $progress->target_date ?? '',
            $progress->status ?? '',
            $progress->remarks ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'System Name',
            'Category',
            'Description',
            'Raised Date',
            'Start Date',
            'Target Date',
            'Status',
            'Remarks',
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 40,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 12,
            'H' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
    }
}

