<?php

namespace App\Exports;

use App\Pdrb;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RevisiTotalExport implements FromView
{
    private $judul;
    private $pdrb;

    public function __construct($judul, $pdrb) {
        $this->judul = $judul;
        $this->pdrb = $pdrb;
    }

    public function view(): View
    {
        return view('exports.revisi_total', ['judul' => $this->judul, 'pdrb' => $this->pdrb]);
    }
}
