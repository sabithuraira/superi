<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class PdrbExport implements WithMultipleSheets{
    private static $wilayah;
    private static $tahun;

    public function __construct($wilayah, $tahun) {
        self::$wilayah = $wilayah;
        self::$tahun = $tahun;
    }

    public function sheets(): array
    {
        $model = new \App\Pdrb();
        $datas = $model->getPdrb(self::$wilayah, self::$tahun);

        $komponen = \App\Komponen::where('status_aktif', 1)->get();

        // return view('exports.pdrb', [
        //     'datas'=>$datas, 
        //     'komponen' => $komponen, 
        //     'tahun' => self::$tahun,
        //     'wilayah' => self::$wilayah,
        // ]);

        $sheets = [
            new PdrbAdhbExport(self::$wilayah, self::$tahun, $komponen, $datas),
            new PdrbAdhkExport(self::$wilayah, self::$tahun, $komponen, $datas),
        ];

        return $sheets;
    }
}

class PdrbAdhbExport implements FromView, WithEvents, WithTitle{
    private $wilayah;
    private $tahun;
    private $komponen;
    private $datas;

    public function __construct($wilayah, $tahun, $komponen, $datas) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->komponen = $komponen;
        $this->datas = $datas;
    }

     /**
     * @return array
     */
    public function registerEvents(): array{
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getStyle('A3:E25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }

    public function view(): View{
        return view('exports.pdrb_adhb', [
            'datas'=>$this->datas, 
            'komponen' => $this->komponen, 
            'tahun' => $this->tahun,
            'wilayah' => $this->wilayah,
        ]);
    }

    public function title(): string{
        return 'adhb';
    }
}

class PdrbAdhkExport implements FromView, WithEvents, WithTitle{
    private $wilayah;
    private $tahun;
    private $komponen;
    private $datas;

    public function __construct($wilayah, $tahun, $komponen, $datas) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->komponen = $komponen;
        $this->datas = $datas;
    }

     /**
     * @return array
     */
    public function registerEvents(): array{
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A3:E25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }

    public function view(): View{
        return view('exports.pdrb_adhk', [
            'datas'=>$this->datas, 
            'komponen' => $this->komponen, 
            'tahun' => $this->tahun,
            'wilayah' => $this->wilayah,
        ]);
    }

    public function title(): string{
        return 'adhk';
    }
}