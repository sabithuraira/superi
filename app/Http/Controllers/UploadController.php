<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PdrbImport;
use App\Imports\FenomenaImport;
use App\Exports\PdrbExport;
use App\Exports\FenomenaExport;
use App\SettingApp;
use PDO;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    //
    public function upload(Request $request)
    {
        $auth = Auth::user();

        $wilayah = $auth->kdkab;
        if ($request->get('wilayah')) {
            $wilayah = $request->get('wilayah');
        }
        // $wilayah = '74';
        $tahun = date('Y');
        $triwulan = 1;

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        $model = new \App\Pdrb();
        return view('upload.upload', compact('model', 'wilayah', 'tahun', 'triwulan'));
    }

    public function import(Request $request)
    {
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;
        $auth = Auth::user();

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        if (strlen($request->get('wilayah')) > 0) {
            $wilayah = $request->get('wilayah');
            // dd($request->get('wilayah'));
        }

        if ($wilayah != $auth->kdkab) {
            if ($wilayah == '12' && $auth->kdkab == '03') {
            } elseif ($wilayah == '13' && $auth->kdkab == '05') {
            } elseif ($auth->kdkab == '00') {
            } else {
                return redirect()->back()->with('error', 'Tidak bisa upload kabupaten lain');
            }
        }

        if (strlen($request->get('tahun')) > 0) {
            $tahun = $request->get('tahun');
        }

        if ($request->get('action') == 1) {
            Excel::import(new PdrbImport($wilayah, $tahun, $triwulan), $request->file('excel_file'));
            return redirect('upload/import')->with('success', 'Data berhasil disimpan');
        } elseif ($request->get('action') == 3 || $request->get('action') == 4) {
            $model = new \App\Pdrb();
            $datas = $model->getPdrb($wilayah, $tahun, $triwulan, 1);

            foreach ($datas['adhb'] as $item) {
                if ($item != null) {
                    $curData = \App\Pdrb::where('id', $item->id)->first();
                    if ($curData) {
                        if ($request->get('action') == 3) {
                            $curData->status_data = 2;
                            $curData->time_approve_provinsi = now();
                        }
                        //approve by provinsi
                        else {
                            $curData->status_data = 1;
                            $curData->updated_at = now();
                        }
                        $curData->save();
                    }
                }
            }

            foreach ($datas['adhk'] as $item) {
                if ($item != null) {
                    $curData = \App\Pdrb::where('id', $item->id)->first();
                    if ($curData) {
                        if ($request->get('action') == 3) {
                            $curData->status_data = 2;
                            $curData->time_approve_provinsi = now();
                        }
                        //approve by provinsi
                        else {
                            $curData->status_data = 1;
                            $curData->updated_at = now();
                        }
                        $curData->save();
                    }
                }
            }

            return redirect('upload/import?wilayah=' . $wilayah)->with('success', 'Data berhasil disimpan');
        } else {
            $str_date = date('Y-m-d h:i:s');
            return Excel::download(new PdrbExport($wilayah), 'pdrb_' . $str_date . '.xlsx');
        }
    }

    public function approve_admin(Request $request)
    {
        $tahun = date('Y');
        $triwulan = 1;

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        //////////////
        $list_wilayah = config('app.wilayah');

        $model = new \App\Pdrb();
        foreach ($list_wilayah as $idx => $val) {
            $datas = $model->getPdrb($idx, $tahun, $triwulan, 2);

            foreach ($datas['adhb'] as $item) {
                if ($item != null) {
                    $curData = \App\Pdrb::where('id', $item->id)->first();
                    if ($curData) {
                        if ($request->get('action') == 1) {
                            $curData->status_data = 3; //approve by admin
                            $curData->time_approve_admin = now();
                            // $curData->putaran = $curData->putaran + 1;
                        } elseif ($request->get('action') == 3) {
                            $curData->status_data = 4; //reject by admin
                            $curData->time_reject = now();
                            // $curData->putaran = $curData->putaran + 1;
                        } else {
                            $curData->status_data = 2; //back to approve provinsi
                            $curData->time_approve_provinsi = now();
                            // $curData->putaran = ($curData->putaran>0) ? ($curData->putaran - 1) : 0;
                        }
                        $curData->save();
                    }
                }
            }

            foreach ($datas['adhk'] as $item) {
                if ($item != null) {
                    $curData = \App\Pdrb::where('id', $item->id)->first();
                    if ($curData) {
                        if ($request->get('action') == 1) {
                            $curData->status_data = 3; //approve by admin
                            $curData->time_approve_admin = now();
                            // $curData->putaran = $curData->putaran + 1;
                        } elseif ($request->get('action') == 3) {
                            $curData->status_data = 4; //reject by admin
                            $curData->time_reject = now();
                            // $curData->putaran = $curData->putaran + 1;
                        } else {
                            $curData->status_data = 2;
                            $curData->time_approve_provinsi = now();
                            // $curData->putaran = ($curData->putaran>0) ? ($curData->putaran - 1) : 0;
                        }
                        $curData->save();
                    }
                }
            }
        }

        return redirect('beranda')->with('success', 'Proses Approve Admin Berhasil');
    }

    public function pdrb(Request $request)
    {
        $datas = [];
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        if (strlen($request->get('wilayah')) > 0) {
            $wilayah = $request->get('wilayah');
        }
        if (strlen($request->get('tahun')) > 0) {
            $tahun = $request->get('tahun');
        }
        // dd($triwulan);
        $model = new \App\Pdrb();
        $datas = $model->getPdrb($wilayah, $tahun, $triwulan, 0);
        $data_rilis = $model->getPdrb($wilayah, $tahun, $triwulan, 2);

        $komponen = \App\Komponen::where('status_aktif', 1)->orderBy('no_komponen')->get();
        // dd($datas);
        return response()->json(['success' => '1', 'datas' => $datas, 'data_rilis' => $data_rilis, 'komponen' => $komponen]);
    }

    public function isAllApprove(Request $request)
    {
        $resultProvinsi = true;
        $resultAdmin = true;
        // $isDataKosong = false;

        $datas = [];
        $tahun = date('Y');
        $triwulan = 1;
        $list_wilayah = config('app.wilayah');

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        $model = new \App\Pdrb();
        foreach ($list_wilayah as $idx => $val) {
            if (!$resultProvinsi && !$resultAdmin) {
                break;
            }

            $datas = $model->getPdrb($idx, $tahun, $triwulan, 2);

            /////////////
            $total_data = $triwulan;
            if ($triwulan == 4) {
                $total_data = 12;
            }

            for ($i = 0; $i < $total_data; ++$i) {
                $el_adhb = $datas['adhb'][$i];
                $el_adhk = $datas['adhk'][$i];

                if ($el_adhb == null || $el_adhk == null) {
                    $isDataKosong = true;
                }
                if ($el_adhb == null || $el_adhk == null || $el_adhb->status_data != 2 || $el_adhk->status_data != 2) {
                    $resultProvinsi = false;
                }
                if ($el_adhb == null || $el_adhk == null || $el_adhb->status_data != 3 || $el_adhk->status_data != 3) {
                    $resultAdmin = false;
                }
                // if(!$resultProvinsi && !$resultAdmin) break;

                // if(el_adhb==null || el_adhk==null || el_adhb['status_data']==2 || el_adhk['status_data']==2) self.isApproveAdmin = false;
            }
            ////////////

            // foreach($datas['adhb'] as $val2){
            //     // if($val2==null) $isDataKosong = true;
            //     if($val2==null || $val2->status_data<2) $resultProvinsi = false;
            //     if($val2==null || $val2->status_data<3) $resultAdmin = false;
            //     // if(!$resultProvinsi && !$resultAdmin) break;
            // }

            // foreach($datas['adhk'] as $val2){
            //     // if($val2==null) $isDataKosong = true;
            //     if($val2==null || $val2->status_data<2) $resultProvinsi = false;
            //     if($val2==null || $val2->status_data<3) $resultAdmin = false;
            //     // if(!$resultProvinsi && !$resultAdmin) break;
            // }
        }

        return response()->json([
            'success' => '1',
            'resultProvinsi' => $resultProvinsi,
            'resultAdmin' => $resultAdmin,
        ]);
    }

    //
    public function fenomena_upload()
    {
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        $model = new \App\Fenomena();
        return view('upload.fenomena', compact('model', 'wilayah', 'tahun', 'triwulan'));
    }

    public function fenomena_import(Request $request)
    {
        $wilayah = '00';
        $tahun = date('Y');
        $triwulan = 1;

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }

        if (strlen($request->get('wilayah')) > 0) {
            $wilayah = $request->get('wilayah');
        }
        if (strlen($request->get('tahun')) > 0) {
            $tahun = $request->get('tahun');
        }

        if ($request->get('action') == 1) {
            Excel::import(new FenomenaImport($wilayah, $tahun, $triwulan), $request->file('excel_file'));
            return redirect('upload/fenomena_import')->with('success', 'Data berhasil disimpan');
        } else {
            $str_date = date('Y-m-d h:i:s');
            return Excel::download(new FenomenaExport($wilayah, $tahun, $triwulan), 'fenomena_' . $str_date . '.xlsx');
        }
    }

    public function fenomena(Request $request)
    {
        $datas = [];
        $wilayah = '00';
        $tahun = date('Y');

        if (strlen($request->get('wilayah')) > 0) {
            $wilayah = $request->get('wilayah');
        }
        if (strlen($request->get('tahun')) > 0) {
            $tahun = $request->get('tahun');
        }

        $model = new \App\Fenomena();
        $datas = $model->getFenomena($wilayah, $tahun);

        $komponen = \App\Komponen::where('status_aktif', 1)->get();

        return response()->json(['success' => '1', 'datas' => $datas, 'komponen' => $komponen]);
    }
}
