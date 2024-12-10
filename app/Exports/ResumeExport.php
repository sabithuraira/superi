<?php

namespace App\Exports;

use App\Pdrb;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ResumeExport implements FromView
{
    private $judul;
    private $columns;
    private $pdrb;

    public function __construct($judul, $columns, $pdrb) {
        $this->judul = $judul;
        $this->columns = $columns;
        $this->pdrb = $pdrb;
    }

    public function view(): View
    {
        return view('exports.resume', ['judul' => $this->judul, 'columns' => $this->columns, 'pdrb' => $this->pdrb]);
    }
}
