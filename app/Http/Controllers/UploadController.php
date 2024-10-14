<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PdrbImport;

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
}
