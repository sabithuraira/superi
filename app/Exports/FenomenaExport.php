<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class FenomenaExport implements FromView, WithEvents, WithTitle{
    private static $wilayah;
    private static $tahun;
    private static $triwulan;

    public function __construct($wilayah, $tahun, $triwulan) {
        self::$wilayah = $wilayah;
        self::$tahun = $tahun;
        self::$triwulan = $triwulan;
    }

     /**
     * @return array
     */
    public function registerEvents(): array{
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A5:D71')->applyFromArray([
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
        $model = new \App\Fenomena();
        $datas = $model->getFenomena(self::$wilayah, self::$tahun);

        $komponen = \App\Komponen::where('status_aktif', 1)->get();

        return view('exports.fenomena', [
            'datas'=>$datas, 
            'komponen' => $komponen, 
            'tahun' => self::$tahun,
            'wilayah' => self::$wilayah,
            'triwulan' => self::$triwulan,
        ]);
    }

    public function title(): string{
        return 'fenomena';
    }
}