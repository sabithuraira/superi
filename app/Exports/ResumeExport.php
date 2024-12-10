<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResumeExport implements FromView, WithTitle
{
    private $judul;
    private $columns;
    private $pdrb;
    private $komponen;

    public function __construct($judul, $columns, $pdrb, $komponen) {
        $this->judul = $judul;
        $this->columns = $columns;
        $this->pdrb = $pdrb;
        $this->komponen = $komponen;
    }

    public function view(): View
    {
        return view('exports.resume', ['judul' => $this->judul, 'columns' => $this->columns, 'pdrb' => $this->pdrb]);
    }

    public function title(): string{
        return $this->komponen;
    }
}
