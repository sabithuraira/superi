<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RingkasanExportAll implements FromView
{

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function view(): View
    {
        return view('pdrb_ringkasan.export_all', ['table' => $this->table]);
    }
}
