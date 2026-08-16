<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements Export, WithMultipleSheets
{
    protected array $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function sheets(): array
    {
        $sheets = [new ReportSheet('Summary', $this->report)];

        foreach ($this->report['sections'] as $section) {
            $sheets[] = new ReportSheet($section['title'], $this->report, $section);
        }

        return $sheets;
    }
}
