<?php

namespace App\Http\Controllers;

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
}
