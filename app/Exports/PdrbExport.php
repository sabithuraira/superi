<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\SettingApp;

class PdrbExport implements WithMultipleSheets{
    private static $wilayah;
    private static $tahun;
    private static $triwulan;

    public function __construct($wilayah) {
        self::$wilayah = $wilayah;

        self::$tahun = date('Y');
        self::$triwulan = 1;
        
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if($tahun_berlaku!=null) self::$tahun = $tahun_berlaku->setting_value;
        
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if($triwulan_berlaku!=null) self::$triwulan = $triwulan_berlaku->setting_value;
    }

    public function sheets(): array{
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
            new PdrbAdhbExport(self::$wilayah, self::$tahun, $komponen, $datas, self::$triwulan),
            new PdrbAdhkExport(self::$wilayah, self::$tahun, $komponen, $datas, self::$triwulan),
        ];

        return $sheets;
    }
}

class PdrbAdhbExport implements FromView, WithEvents, WithTitle{
    private $wilayah;
    private $tahun;
    private $triwulan;
    private $komponen;
    private $datas;

    public function __construct($wilayah, $tahun, $komponen, $datas, $triwulan) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->komponen = $komponen;
        $this->datas = $datas;
        $this->triwulan = $triwulan;
    }

     /**
     * @return array
     */
    public function registerEvents(): array{
        $cellSelected = 'A3:E25';
        switch ($this->triwulan) {
            case 1:
                $cellSelected = 'A3:B25';
                break;
            case 2:
                $cellSelected = 'A3:C25';
                break;
            case 3:
                $cellSelected = 'A3:D25';
                break;
            default:
                $cellSelected = 'A3:E25';
        };

        return [
            AfterSheet::class    => function(AfterSheet $event) use ($cellSelected) {
                // $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getStyle($cellSelected)->applyFromArray([
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
            'triwulan' => $this->triwulan,
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
    private $triwulan;
    private $komponen;
    private $datas;

    public function __construct($wilayah, $tahun, $komponen, $datas, $triwulan) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->komponen = $komponen;
        $this->datas = $datas;
        $this->triwulan = $triwulan;
    }

     /**
     * @return array
     */
    public function registerEvents(): array{
        $cellSelected = 'A3:E25';
        switch ($this->triwulan) {
            case 1:
                $cellSelected = 'A3:B25';
                break;
            case 2:
                $cellSelected = 'A3:C25';
                break;
            case 3:
                $cellSelected = 'A3:D25';
                break;
            default:
                $cellSelected = 'A3:E25';
        };
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($cellSelected){
                $event->sheet->getStyle($cellSelected)->applyFromArray([
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
            'triwulan' => $this->triwulan,
        ]);
    }

    public function title(): string{
        return 'adhk';
    }
}