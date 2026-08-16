<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSheet implements FromArray, ShouldAutoSize, WithCustomStartCell, WithEvents, WithStyles, WithTitle
{
    protected string $title;

    protected array $report;

    protected ?array $section;

    public function __construct(string $title, array $report, ?array $section = null)
    {
        $this->title = $title;
        $this->report = $report;
        $this->section = $section;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function array(): array
    {
        $data = [
            ['Avalon Solutions Ltd'],
            [$this->report['title']],
            ['Generated: '.$this->report['generated_at']],
            [],
        ];

        foreach ($this->report['filters'] as $filter) {
            $data[] = [$filter['label'].': '.$filter['value']];
        }

        $data[] = [];

        if ($this->section === null) {
            foreach ($this->report['totals'] as $total) {
                $data[] = [$total['label'], $total['value']];
            }

            return $data;
        }

        $data[] = [$this->section['title']];
        $data[] = $this->section['headers'];

        foreach ($this->section['rows'] as $row) {
            $data[] = array_values($row);
        }

        return $data;
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }

                if ($this->section !== null) {
                    $headersRow = 5 + count($this->report['filters']) + 2;
                    $highestRow = $sheet->getHighestRow();

                    $sheet->getStyle('A'.$headersRow.':'.$sheet->getHighestColumn().$headersRow)
                        ->getFont()->setBold(true)
                        ->getColor()->setARGB('FFFFFF');

                    $sheet->getStyle('A'.$headersRow.':'.$sheet->getHighestColumn().$headersRow)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('343A40');

                    $sheet->getStyle('A'.$headersRow.':'.$sheet->getHighestColumn().$highestRow)
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setARGB('DEE2E6');
                }
            },
        ];
    }
}
