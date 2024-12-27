<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pdrb extends Model
{
    protected $table = 'pdrb';

    public function getPdrb($wilayah, $tahun, $triwulan){
        $datas = [
            'adhb' => $this->queryPdrb($wilayah, $tahun, $triwulan, 1),
            'adhk' => $this->queryPdrb($wilayah, $tahun, $triwulan, 2),
        ];        

        return $datas;
    }

    private function queryPdrb($wilayah, $tahun, $triwulan, $adhb_or_adhk){
        if($triwulan==4){
            $datas = [null, null, null, null, null, null, null, null, null, null, null, null];

            for($x=($tahun-2);$x<=$tahun;$x++){
                $sql = "SELECT * from superi_pdrb 
                    WHERE 
                        kode_prov='16' AND 
                        kode_kab='$wilayah' AND 
                        tahun='$x' AND 
                        adhb_or_adhk=$adhb_or_adhk AND 
                        status_data=1
                    ORDER BY  q ASC, revisi_ke DESC";

                $result = DB::select(DB::raw($sql));

                $idx = $x - ($tahun-2);
                foreach($result as $value){
                    if($datas[($idx*4)+($value->q-1)]==null){
                        $datas[($idx*4)+($value->q-1)] = $value;
                    }
                }
            }
    
            return $datas;
        }
        else{
            $sql = "SELECT * from superi_pdrb 
                WHERE 
                    kode_prov='16' AND 
                    kode_kab='$wilayah' AND 
                    tahun='$tahun' AND 
                    adhb_or_adhk=$adhb_or_adhk AND 
                    status_data=1
                ORDER BY  q ASC, revisi_ke DESC";

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
}
