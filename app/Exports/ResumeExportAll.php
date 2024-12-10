<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ResumeExport;

class ResumeExportAll implements WithMultipleSheets
{
    private $judul;
    private $columns;
    private $pdrb;

    public function __construct($judul, $columns, $pdrb) {
        $this->judul = $judul;
        $this->columns = $columns;
        $this->pdrb = $pdrb;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->pdrb as $c => $pdrb_c) {
            $sheets[] = new ResumeExport($this->judul, $this->columns, $pdrb_c, $c);
        }

        return $sheets;
    }
}
