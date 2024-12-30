<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    private $data;
    private $report;

    function __construct($data, $report) {
        $this->data = $data;
        $this->report = $report;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('analytics.report.export', [
            'data' => $this->data,
            'report' => $this->report
        ]);
    }
}
