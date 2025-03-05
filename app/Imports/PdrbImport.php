<?php
namespace App\Imports;

use App\Pdrb;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PdrbImport implements  WithMultipleSheets //ToCollection
{
    use Importable;

    private static $wilayah;
    private static $tahun;
    private static $triwulan;

    public function __construct($wilayah, $tahun, $triwulan) {
        self::$wilayah = $wilayah;
        self::$tahun = $tahun;
        self::$triwulan = $triwulan;
    }

    public function sheets(): array
    {
        return [
            0 => new AdhbSheetImport(self::$wilayah,  self::$tahun,  self::$triwulan),
            1 => new AdhkSheetImport(self::$wilayah,  self::$tahun,  self::$triwulan),
        ];
    }

    static function importColumn(Collection $rows, $col_no, $is_adhb){
        $header_pdrb =  $rows[2][$col_no];
        $split_header = explode("Q", $header_pdrb);

        if(count($split_header)==2 && is_numeric($split_header[0]) && is_numeric($split_header[1])){
            $year = $split_header[0];
            $q = $split_header[1];

            $putaran = 0;

            $model = Pdrb::where('tahun', $year) //self::$tahun)
                ->where('q', $q)
                ->where('adhb_or_adhk', $is_adhb)
                ->where('kode_prov', '16')
                ->where('kode_kab', self::$wilayah)
                ->orderBy('id', 'desc')
                ->first();

            $new_model = new Pdrb;
            $new_model->tahun = $year; //self::$tahun;
            $new_model->q = $q;
            $new_model->adhb_or_adhk = $is_adhb;
            $new_model->status_data = 1;

            ///TEMPORARY CODE, IT SHOULD COME FROM AUTH
            $new_model->kode_prov   = '16';
            $new_model->kode_kab   = self::$wilayah;
            $new_model->created_by = 1;
            $new_model->updated_by = 1;
            /////////////////////

            if($model==null){
                $new_model->revisi_ke = 0;
            }
            else{
                $new_model->revisi_ke = 1;
                
                if($model->status_data==4) $putaran = $model->putaran + 1;
                else $putaran = $model->putaran;
            }

            $new_model->putaran = $putaran;
            
            // dd($rows[3]);die();

            $new_model->c_1a     = strval($rows[4][$col_no]);
            $new_model->c_1b     = strval($rows[5][$col_no]);
            $new_model->c_1c     = strval($rows[6][$col_no]);
            $new_model->c_1d     = strval($rows[7][$col_no]);
            $new_model->c_1e     = strval($rows[8][$col_no]);
            $new_model->c_1f     = strval($rows[9][$col_no]);
            $new_model->c_1g     = strval($rows[10][$col_no]);
            $new_model->c_1h     = strval($rows[11][$col_no]);
            $new_model->c_1i     = strval($rows[12][$col_no]);
            $new_model->c_1j     = strval($rows[13][$col_no]);
            $new_model->c_1k     = strval($rows[14][$col_no]);
            $new_model->c_1l     = strval($rows[15][$col_no]);
            $new_model->c_1      = strval($rows[3][$col_no]);

            $new_model->c_2     = strval($rows[16][$col_no]);

            $new_model->c_3     = strval($rows[17][$col_no]);
            $new_model->c_3a     = 0;
            $new_model->c_3b     = 0;

            $new_model->c_4     = strval($rows[18][$col_no]);
            $new_model->c_4a     = strval($rows[19][$col_no]);
            $new_model->c_4b     = strval($rows[20][$col_no]);

            $new_model->c_5     = strval($rows[21][$col_no]);

            $new_model->c_6     = strval($rows[22][$col_no]);
            $new_model->c_6a     = 0;
            $new_model->c_6b     = 0;

            $new_model->c_7     = strval($rows[23][$col_no]);
            $new_model->c_7a     = 0;
            $new_model->c_7b     = 0;

            $new_model->c_8     = 0;
            $new_model->c_8a     = 0;
            $new_model->c_8b     = 0;
            $new_model->c_pdrb     = strval($rows[24][$col_no]);

            try {
                $new_model->save();
            } catch (\Exception $e) {
              
                return $e->getMessage();
            }
        }
    }
}

class AdhbSheetImport implements ToCollection
{
    public $wilayah;
    public $tahun;
    public $triwulan;

    public function __construct($wilayah, $tahun, $triwulan) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->triwulan = $triwulan;
    }

    public function collection(Collection $rows){
        $max = 12;
        if($this->triwulan<4){
            $max = $this->triwulan;
        }
        
        for($i=1;$i<=$max;++$i){
            if(count($rows[2])>$i) (new PdrbImport($this->wilayah,  $this->tahun,  $this->triwulan))->importColumn($rows, $i, 1);
        }
    }
}

class AdhkSheetImport implements ToCollection
{
    public $wilayah;
    public $tahun;
    public $triwulan;

    public function __construct($wilayah, $tahun, $triwulan) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->triwulan = $triwulan;
    }

    public function collection(Collection $rows)
    {
        $max = 12;
        if($this->triwulan<4){
            $max = $this->triwulan;
        }
        for($i=1;$i<=$max;++$i){
            if(count($rows[2])>$i) (new PdrbImport($this->wilayah,  $this->tahun,  $this->triwulan))->importColumn($rows, $i, 2);
        }
    }
}