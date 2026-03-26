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
        return Progress::with(['systems.team', 'category'])
            ->useFilters()
            ->get();
    }

    public function map($progress): array
    {
        return [
            $progress->systems ? $progress->systems->name : 'N/A',
            $progress->systems && $progress->systems->team ? $progress->systems->team->pluck('name')->join(', ') : 'N/A',
            $progress->category ? $progress->category->name : 'N/A',
            $progress->description ?? '',
            $progress->raised_date ?? '',
            $progress->target_date ?? '',
            $progress->end_date ?? '',
            $progress->status ?? '',
            $progress->remarks ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'System Name',
            'Team Name',
            'Category',
            'Description',
            'Remarks',
            'Raised Date',
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
            'E' => 30,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        
        $sheet->getStyle('A1:I1')->applyFromArray([
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

