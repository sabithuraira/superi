<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class RevisiTotalExport implements FromView, WithTitle
{
    private $judul;
    private $pdrb;
    private $kd_kab;

    public function __construct($judul, $pdrb, $kd_kab) {
        $this->judul = $judul;
        $this->pdrb = $pdrb;
        $this->kd_kab = $kd_kab;
    }

    public function view(): View
    {
        return view('exports.revisi_total', ['judul' => $this->judul, 'pdrb' => $this->pdrb]);
    }

    public function title(): string{
        return $this->kd_kab;
    }
}
