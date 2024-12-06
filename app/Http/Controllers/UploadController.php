<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PdrbImport;
use App\Imports\FenomenaImport;
use App\Exports\PdrbExport;
use App\Exports\FenomenaExport;
use App\SettingApp;

class UploadController extends Controller
{
    // 
    public function upload(){
        $wilayah = '00';
        $tahun = date('Y');
        
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if($tahun_berlaku!=null) $tahun = $tahun_berlaku->setting_value;

        $model = new \App\Pdrb();
        return view('upload.upload',compact('model', 'wilayah', 'tahun'));
    }

    public function import(Request $request){
        $wilayah = '00';
        $tahun = date('Y');

        if (strlen($request->get('wilayah')) > 0) $wilayah = $request->get('wilayah');
        if (strlen($request->get('tahun')) > 0) $tahun = $request->get('tahun');

        if($request->get('action')==1){
            Excel::import(new PdrbImport($wilayah, $tahun), $request->file('excel_file'));
            return redirect('upload/import')->with('success', 'Data berhasil disimpan');
        }
        else{
            $str_date = date('Y-m-d h:i:s');
            return Excel::download(new PdrbExport($wilayah, $tahun), "pdrb_".$str_date.".xlsx");
        }
    }

    public function pdrb(Request $request){
        $datas=array();
        $wilayah = '00';
        $tahun = date('Y');

        if (strlen($request->get('wilayah')) > 0) $wilayah = $request->get('wilayah');
        if (strlen($request->get('tahun')) > 0) $tahun = $request->get('tahun');

        $model = new \App\Pdrb();
        $datas = $model->getPdrb($wilayah, $tahun);

        $komponen = \App\Komponen::where('status_aktif', 1)->get();
        
        return response()->json(['success'=>'1', 'datas'=>$datas, 'komponen' => $komponen]);
    }
    
    // 
    public function fenomena_upload(){
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;
        
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if($tahun_berlaku!=null) $tahun = $tahun_berlaku->setting_value;
        
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if($triwulan_berlaku!=null) $triwulan = $triwulan_berlaku->setting_value;

        $model = new \App\Fenomena();
        return view('upload.fenomena',compact('model', 'wilayah', 'tahun', 'triwulan'));
    }

    public function fenomena_import(Request $request){
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;
        
        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if($triwulan_berlaku!=null) $triwulan = $triwulan_berlaku->setting_value;

        if (strlen($request->get('wilayah')) > 0) $wilayah = $request->get('wilayah');
        if (strlen($request->get('tahun')) > 0) $tahun = $request->get('tahun');


        if($request->get('action')==1){
            Excel::import(new FenomenaImport($wilayah, $tahun, $triwulan), $request->file('excel_file'));
            return redirect('upload/fenomena_import')->with('success', 'Data berhasil disimpan');
        }
        else{
            $str_date = date('Y-m-d h:i:s');
            return Excel::download(new FenomenaExport($wilayah, $tahun, $triwulan), "fenomena_".$str_date.".xlsx");
        }
    }

    public function fenomena(Request $request){
        $datas=array();
        $wilayah = '00';
        $tahun = date('Y');

        if (strlen($request->get('wilayah')) > 0) $wilayah = $request->get('wilayah');
        if (strlen($request->get('tahun')) > 0) $tahun = $request->get('tahun');

        $model = new \App\Fenomena();
        $datas = $model->getFenomena($wilayah, $tahun);

        $komponen = \App\Komponen::where('status_aktif', 1)->get();
        
        return response()->json(['success'=>'1', 'datas'=>$datas, 'komponen' => $komponen]);
    }
}
