<?php

namespace App\Http\Controllers;

use App\Pdrb;
use Illuminate\Http\Request;
use App\SettingApp;

class SettingAppController extends Controller
{
    //
    public function index(){
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();

        return view('setting_app.index',compact('tahun_berlaku', 'triwulan_berlaku'));
    }

    public function store(Request $request){
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();

        if (strlen($request->get('tahun_berlaku')) > 0){
            $tahun_berlaku->setting_value = $request->get('tahun_berlaku');
            $tahun_berlaku->save();
        }

        if (strlen($request->get('triwulan_berlaku')) > 0){
            $triwulan_berlaku->setting_value = $request->get('triwulan_berlaku');
            $triwulan_berlaku->save();
        }

        return redirect('setting_app')->with('success', 'Data berhasil disimpan');
    }

    public function cleaning_data(Request $request){
        $list_wilayah = config('app.wilayah');
        foreach ($list_wilayah as $id_wil => $wilayah) {
            // dd($id_wil);
            for($q=1 ; $q<=4 ;$q++){
                for($k=1;$k<=2;$k++){
                    $data = Pdrb::where('kode_kab', $id_wil)->where('tahun', $request->tahun)->where('q', $q)->where('adhb_or_adhk', $k)->orderby('revisi_ke', 'desc')->first();
                    if($data){
                        $max_rev = $data->revisi_ke;
                        // dd($max_rev);
                        for($r=0;$r<=$max_rev;$r++){
                            $data2= Pdrb::where('kode_kab', $id_wil)->where('tahun', $request->tahun)->where('q', $q)->where('adhb_or_adhk', $k)->where('revisi_ke',$r)->orderby('id', 'desc')->get();
                            if ($data2->count() > 1) {
                                foreach ($data2->slice(1) as $dt2) {
                                    $dt2->delete();
                                }
                            }
                        }

                    }

                }
            }
        }
    }

}
