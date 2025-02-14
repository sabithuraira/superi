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
    private $triwulan;
    
    public function __construct($wilayah, $tahun, $triwulan) {
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
        $this->triwulan = $triwulan;
    }
    
    public function collection(Collection $rows){
        $this->importColumn($rows, "q-to-q");
        $this->importColumn($rows, "y-o-y");
        $this->importColumn($rows, "c-to-c");
    }

    function importColumn(Collection $rows, $type_pertumbuhan){
        $model = Fenomena::where('tahun', $this->tahun)
            ->where('q', $this->triwulan)
            ->where('kode_prov', '16')
            ->where('kode_kab', $this->wilayah)
            ->where('pertumbuhan', $type_pertumbuhan)
            ->first();

        if($model==null){
            $model = new Fenomena;
            $model->tahun = $this->tahun;
            $model->pertumbuhan =  $type_pertumbuhan;
            $model->q = $this->triwulan;
            $model->kode_prov   = '16';
            $model->kode_kab   = $this->wilayah;
            $model->created_by = 1;
        }
        
        $model->updated_by = 1;

        $start_from = 5;
        if($type_pertumbuhan=="y-o-y") $start_from = 6;
        else if($type_pertumbuhan=="c-to-c")  $start_from = 7;
        
        $model->fenomena_c_1     = ($rows[$start_from][3]=="") ? "" : str_replace('"', "'", $rows[$start_from][3]); 
        $model->fenomena_c_1a     = ($rows[($start_from + 3 * 1)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 1)][3]);
        $model->fenomena_c_1b     = ($rows[($start_from + 3 * 2)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 2)][3]); 
        $model->fenomena_c_1c     = ($rows[($start_from + 3 * 3)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 3)][3]); 
        $model->fenomena_c_1d     = ($rows[($start_from + 3 * 4)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 4)][3]); 
        $model->fenomena_c_1e     = ($rows[($start_from + 3 * 5)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 5)][3]); 
        $model->fenomena_c_1f     = ($rows[($start_from + 3 * 6)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 6)][3]); 
        $model->fenomena_c_1g     = ($rows[($start_from + 3 * 7)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 7)][3]); 
        $model->fenomena_c_1h     = ($rows[($start_from + 3 * 8)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 8)][3]); 
        $model->fenomena_c_1i     = ($rows[($start_from + 3 * 9)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 9)][3]); 
        $model->fenomena_c_1j     = ($rows[($start_from + 3 * 10)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 10)][3]); 
        $model->fenomena_c_1k     = ($rows[($start_from + 3 * 11)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 11)][3]); 
        $model->fenomena_c_1l     = ($rows[($start_from + 3 * 12)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 12)][3]); 

        $model->fenomena_c_2     = ($rows[($start_from + 3 * 13)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 13)][3]); 

        $model->fenomena_c_3     = ($rows[($start_from + 3 * 14)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 14)][3]); 
        $model->fenomena_c_3a     = "";
        $model->fenomena_c_3b     = "";

        $model->fenomena_c_4     = ($rows[($start_from + 3 * 15)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 15)][3]); 
        $model->fenomena_c_4a     = ($rows[($start_from + 3 * 16)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 16)][3]); 
        $model->fenomena_c_4b     = ($rows[($start_from + 3 * 17)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 17)][3]); 

        $model->fenomena_c_5     = ($rows[($start_from + 3 * 18)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 18)][3]); 

        $model->fenomena_c_6     = ($rows[($start_from + 3 * 19)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 19)][3]); 
        $model->fenomena_c_6a     = "";
        $model->fenomena_c_6b     = "";

        $model->fenomena_c_7     = ($rows[($start_from + 3 * 20)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 20)][3]); 
        $model->fenomena_c_7a     = "";
        $model->fenomena_c_7b     = "";

        $model->fenomena_c_8     = "";
        $model->fenomena_c_8a     = "";
        $model->fenomena_c_8b     = "";
        $model->fenomena_c_pdrb     = ($rows[($start_from + 3 * 21)][3]=="") ? "" : str_replace('"', "'", $rows[($start_from + 3 * 21)][3]); 
        $model->save();
    }
}