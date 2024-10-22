<?php
namespace App\Imports;

use App\Fenomena;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;

class FenomenaImport implements ToCollection
{
    use Importable;

    private $wilayah;
    private $tahun;
    
    public function __construct($wilayah, $tahun) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
    }
    
    public function collection(Collection $rows){
        $this->importColumn($rows, "q-to-q");
        $this->importColumn($rows, "y-o-y");
        $this->importColumn($rows, "c-to-c");
    }

    function importColumn(Collection $rows, $type_pertumbuhan){
        // $year = $rows[2][2];
        $q = str_replace("Q", "", $rows[3][2]);

        $model = Fenomena::where('tahun', $this->tahun)
            ->where('q', $q)
            ->where('kode_prov', '16')
            ->where('kode_kab', $this->wilayah)
            ->where('pertumbuhan', $type_pertumbuhan)
            ->first();

        if($model==null){
            $model = new Fenomena;
            $model->tahun = $this->tahun;
            $model->pertumbuhan =  $type_pertumbuhan;
            $model->q = $q;
            $model->kode_prov   = '16';
            $model->kode_kab   = $this->wilayah;
            $model->created_by = 1;
        }
        
        $model->updated_by = 1;

        $start_from = 5;
        if($type_pertumbuhan=="y-o-y") $start_from = 6;
        else if($type_pertumbuhan=="c-to-c")  $start_from = 7;
        
        $model->fenomena_c_1     = $rows[$start_from][9];
        $model->fenomena_c_1a     = $rows[($start_from + 3 * 1)][9];
        $model->fenomena_c_1b     = $rows[($start_from + 3 * 2)][9];
        $model->fenomena_c_1c     = $rows[($start_from + 3 * 3)][9];
        $model->fenomena_c_1d     = $rows[($start_from + 3 * 4)][9];
        $model->fenomena_c_1e     = $rows[($start_from + 3 * 5)][9];
        $model->fenomena_c_1f     = $rows[($start_from + 3 * 6)][9];
        $model->fenomena_c_1g     = $rows[($start_from + 3 * 7)][9];
        $model->fenomena_c_1h     = $rows[($start_from + 3 * 8)][9];
        $model->fenomena_c_1i     = $rows[($start_from + 3 * 9)][9];
        $model->fenomena_c_1j     = $rows[($start_from + 3 * 10)][9];
        $model->fenomena_c_1k     = $rows[($start_from + 3 * 11)][9];
        $model->fenomena_c_1l     = $rows[($start_from + 3 * 12)][9];

        $model->fenomena_c_2     = $rows[($start_from + 3 * 13)][9];

        $model->fenomena_c_3     = $rows[($start_from + 3 * 14)][9];
        $model->fenomena_c_3a     = $rows[($start_from + 3 * 15)][9];
        $model->fenomena_c_3b     = $rows[($start_from + 3 * 16)][9];

        $model->fenomena_c_4     = $rows[($start_from + 3 * 17)][9];
        $model->fenomena_c_4a     = $rows[($start_from + 3 * 18)][9];
        $model->fenomena_c_4b     = $rows[($start_from + 3 * 19)][9];

        $model->fenomena_c_5     = $rows[($start_from + 3 * 20)][9];

        $model->fenomena_c_6     = $rows[($start_from + 3 * 21)][9];
        $model->fenomena_c_6a     = $rows[($start_from + 3 * 22)][9];
        $model->fenomena_c_6b     = $rows[($start_from + 3 * 23)][9];

        $model->fenomena_c_7     = $rows[($start_from + 3 * 24)][9];
        $model->fenomena_c_7a     = $rows[($start_from + 3 * 25)][9];
        $model->fenomena_c_7b     = $rows[($start_from + 3 * 26)][9];

        $model->fenomena_c_8     = $rows[($start_from + 3 * 27)][9];
        $model->fenomena_c_8a     = $rows[($start_from + 3 * 28)][9];
        $model->fenomena_c_8b     = $rows[($start_from + 3 * 29)][9];
        $model->fenomena_c_pdrb     = $rows[($start_from + 3 * 30)][9];
        $model->save();
    }
}