<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PdrbFinal extends Model
{
    protected $table = 'pdrb_final';

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
                $sql = "SELECT * from superi_pdrb_final 
                    WHERE 
                        kode_prov='16' AND 
                        kode_kab='$wilayah' AND 
                        tahun='$x' AND 
                        adhb_or_adhk=$adhb_or_adhk AND 
                        status_data=1
                    ORDER BY  q ASC";

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
            $sql = "SELECT * from superi_pdrb_final 
                WHERE 
                    kode_prov='16' AND 
                    kode_kab='$wilayah' AND 
                    tahun='$tahun' AND 
                    adhb_or_adhk=$adhb_or_adhk AND 
                    status_data=1
                ORDER BY  q ASC";

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

    public function getResumeBeranda($wilayah){
        $tahun = date('Y');
        $triwulan = 1;
        
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if($tahun_berlaku!=null) $tahun = $tahun_berlaku->setting_value;
        
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if($triwulan_berlaku!=null) $triwulan = $triwulan_berlaku->setting_value;

        $data = $this->getPdrb($wilayah, $tahun, 4);

        // dd($data);die();

        $qtoq = ($data['adhk'][4*2+$triwulan-1]==null) ? "" : $data['adhk'][4*2+$triwulan-1]->c_pdrb/$data['adhk'][4*2+$triwulan-2]->c_pdrb*100-100;
        $yony = ($data['adhk'][4*2+$triwulan-1]==null) ? "" : $data['adhk'][4*2+$triwulan-1]->c_pdrb/$data['adhk'][4*1+$triwulan-1]->c_pdrb*100-100;
        $cum_y = 0;
        $cum_y_min1 = 0;

        for($x=0;$x<$triwulan;$x++){
            if($data['adhk'][4*2+$x]!=null) $cum_y += $data['adhk'][4*2+$x]->c_pdrb;
            if($data['adhk'][4*1+$x]!=null) $cum_y_min1 += $data['adhk'][4*1+$x]->c_pdrb;
        }

        $ctoc = ($cum_y==0) ? "" : $cum_y/$cum_y_min1*100-100;
        
        return [
            'ctoc' => round($ctoc, 4),
             'qtoq' => round($qtoq,4), 
             'yony' => round($yony,4),
        ];
    }
}
