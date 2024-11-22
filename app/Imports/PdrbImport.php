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

    public function __construct($wilayah, $tahun) {
        self::$wilayah = $wilayah;
        self::$tahun = $tahun;
    }

    public function sheets(): array
    {
        return [
            0 => new AdhbSheetImport(self::$wilayah,  self::$tahun),
            1 => new AdhkSheetImport(self::$wilayah,  self::$tahun),
        ];
    }

    static function importColumn(Collection $rows, $col_no, $is_adhb){
        $header_pdrb =  $rows[2][$col_no];
        $split_header = explode("Q", $header_pdrb);

        if(count($split_header)==2 && is_numeric($split_header[0]) && is_numeric($split_header[1])){
            // $year = $split_header[0];
            $q = $split_header[1];

            $model = Pdrb::where('tahun', self::$tahun)
                ->where('q', $q)
                ->where('adhb_or_adhk', $is_adhb)
                ->where('kode_prov', '16')
                ->where('kode_kab', self::$wilayah)
                ->first();

            $new_model = new Pdrb;
            $new_model->tahun =self::$tahun;
            $new_model->q = $q;
            $new_model->adhb_or_adhk = $is_adhb;
            $new_model->status_data = 1;

            ///TEMPORARY CODE, IT SHOULD COME FROM AUTH
            $new_model->kode_prov   = '16';
            $new_model->kode_kab   = self::$wilayah;
            $new_model->created_by = 1;
            $new_model->updated_by = 1;
            /////////////////////

            if($model==null) $new_model->revisi_ke = 0;
            else $new_model->revisi_ke = 1;
            
            $new_model->c_1a     = $rows[4][$col_no];
            $new_model->c_1b     = $rows[5][$col_no];
            $new_model->c_1c     = $rows[6][$col_no];
            $new_model->c_1d     = $rows[7][$col_no];
            $new_model->c_1e     = $rows[8][$col_no];
            $new_model->c_1f     = $rows[9][$col_no];
            $new_model->c_1g     = $rows[10][$col_no];
            $new_model->c_1h     = $rows[11][$col_no];
            $new_model->c_1i     = $rows[12][$col_no];
            $new_model->c_1j     = $rows[13][$col_no];
            $new_model->c_1k     = $rows[14][$col_no];
            $new_model->c_1l     = $rows[15][$col_no];
            $new_model->c_1     = $rows[3][$col_no];

            $new_model->c_2     = $rows[16][$col_no];

            $new_model->c_3     = $rows[17][$col_no];
            $new_model->c_3a     = 0;
            $new_model->c_3b     = 0;

            $new_model->c_4     = $rows[18][$col_no];
            $new_model->c_4a     = $rows[19][$col_no];
            $new_model->c_4b     = $rows[20][$col_no];

            $new_model->c_5     = $rows[21][$col_no];

            $new_model->c_6     = $rows[22][$col_no];
            $new_model->c_6a     = 0;
            $new_model->c_6b     = 0;

            $new_model->c_7     = $rows[23][$col_no];
            $new_model->c_7a     = 0;
            $new_model->c_7b     = 0;

            $new_model->c_8     = 0;
            $new_model->c_8a     = 0;
            $new_model->c_8b     = 0;
            $new_model->c_pdrb     = $rows[24][$col_no];

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

    public function __construct($wilayah, $tahun) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows){
        for($i=1;$i<=4;++$i){
            if(count($rows[2])>$i) (new PdrbImport($this->wilayah,  $this->tahun))->importColumn($rows, $i, 1);
        }
    }
}

class AdhkSheetImport implements ToCollection
{
    public $wilayah;
    public $tahun;

    public function __construct($wilayah, $tahun) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        for($i=1;$i<=4;++$i){
            if(count($rows[2])>$i) (new PdrbImport($this->wilayah,  $this->tahun))->importColumn($rows, $i, 2);
        }
    }
}