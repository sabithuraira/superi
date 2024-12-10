<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\RevisiTotalExport;

class RevisiTotalExportAll implements WithMultipleSheets
{
    private $judul;
    private $pdrb;

    public function __construct($judul, $pdrb) {
        $this->judul = $judul;
        $this->pdrb = $pdrb;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->pdrb as $kd_kab => $pdrb_kd_kab) {
            $sheets[] = new RevisiTotalExport($this->judul, $pdrb_kd_kab, $kd_kab);
        }

        return $sheets;
    }
}
