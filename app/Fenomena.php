<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fenomena extends Model
{
    protected $table = 'fenomena';

    public function getFenomena($wilayah, $tahun){
        $datas = [
            'q-to-q' => $this->queryFenomena($wilayah, $tahun, 'q-to-q'),
            'y-o-y' => $this->queryFenomena($wilayah, $tahun, 'y-o-y'),
            'c-to-c' => $this->queryFenomena($wilayah, $tahun, 'c-to-c'),
        ];        

        return $datas;
    }

    private function queryFenomena($wilayah, $tahun, $pertumbuhan){
        $sql = "SELECT * from superi_fenomena 
            WHERE 
                kode_prov='16' AND 
                kode_kab='$wilayah' AND 
                tahun='$tahun' AND 
                pertumbuhan='$pertumbuhan' 
            ORDER BY q ASC";
            
        $datas = [null, null, null, null];
        
        $result = DB::select(DB::raw($sql));

        foreach($result as $value){
            if($datas[$value->q-1]==null){
                $datas[$value->q-1] = $value;
            }
        }

        return $datas;
    }
}
