<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PdrbImport;
use App\Imports\FenomenaImport;

class UploadController extends Controller
{
    // 
    public function upload(){
        $model = new \App\Pdrb();
        return view('upload.upload',compact('model'));
    }

    public function import(Request $request){
        Excel::import(new PdrbImport(), $request->file('excel_file'));
        return redirect('upload/import')->with('success', 'Information has been added');
    }

    
    // 
    public function fenomena_upload(){
        $model = new \App\Fenomena();
        return view('upload.fenomena',compact('model'));
    }

    public function fenomena_import(Request $request){
        Excel::import(new FenomenaImport(), $request->file('excel_file'));
        return redirect('upload/fenomena_import')->with('success', 'Information has been added');
    }
}
