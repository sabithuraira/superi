<?php

namespace App\Http\Controllers;

use App\Komponen;
use App\Pdrb;
use App\PdrbFinal;
use Illuminate\Http\Request;
use App\SettingApp;
use Illuminate\Support\Facades\Auth;

class PdrbPutaranController extends Controller
{
    //
    public $list_wilayah;
    public $tahun_berlaku;
    public $triwulan_berlaku;
    public $list_periode = [];
    // public $list_periode = [
    //     '2024Q1', '2024Q2', '2024Q3', '2024Q4'
    // ];

    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
        $this->tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first()->setting_value;
        $this->triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first()->setting_value;
        for ($i = 2010; $i <= $this->tahun_berlaku; $i++) {
            if ($i > 2017) {
                array_push($this->list_periode, "{$i}Q1");
                array_push($this->list_periode, "{$i}Q2");
                array_push($this->list_periode, "{$i}Q3");
                array_push($this->list_periode, "{$i}Q4");
                array_push($this->list_periode, "{$i}");
            } else {
                array_push($this->list_periode, "{$i}");
            }
        }
    }

    public $list_tabel = [
        [
            'id' => '3.1',
            'name' => 'Tabel 3.1. PDRB ADHB Menurut Pengeluaran, (Juta Rp)'
        ],
        [
            'id' => '3.2',
            'name' => 'Tabel 3.2. PDRB ADHK Menurut Pengeluaran, (Juta Rp)'
        ],
    ];

    public function index(Request $request, $id){
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $tahun_berlaku = $this->tahun_berlaku;

        $max_putaran = Pdrb::where('tahun', $tahun_berlaku)->max('putaran');

        $list_detail_komponen = Komponen::where('status_aktif', 1)
                                    ->orderby('no_komponen')->get();
        $list_wilayah = $this->list_wilayah;
        $tabel_filter = $id; //$request->tabel_filter ? $request->tabel_filter : '3.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : $list_periode;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : Auth::user()->kdkab;
        $putaran_filter = $request->putaran_filter ? $request->putaran_filter : '0';

        $adhb_or_adhk = ($id === '3.1') ? 1 : 2;
        $data = [];

        if(!is_array($periode_filter)){
            $test_explode = explode(",", $periode_filter);
            if(count($test_explode)>1) $periode_filter = $test_explode;
        }

        // dd($putaran_filter);
        
        foreach ($list_detail_komponen as $komponen) {
            $row = [];
            $row = [ 'id' => $komponen['no_komponen'], 'name' => $komponen['nama_komponen']];
            
            $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
            // if ($id == '3.2') $komp_id = $komponen['no_komponen'];

            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);
                if(count($arr_periode)>1) {
                    $condition_arr = [];
                    $condition_arr[] = ['kode_kab', '=', $wilayah_filter];
                    $condition_arr[] = ['tahun', '=', $arr_periode[0]];
                    $condition_arr[] = ['q', '=', $arr_periode[1]];
                    $condition_arr[] = ['adhb_or_adhk', $adhb_or_adhk];
                    $condition_arr[] = ['putaran', $putaran_filter];
                    // $condition_arr[] = ['status_data','>' ,1];
    
                    $data_y = Pdrb::where($condition_arr)
                        ->orderby('id', 'desc')
                        ->first();
                    $row[$periode] = $data_y ? $data_y->$komp_id : null;
                }
            }
            $data[] = $row;
        }

        return view('pdrb_putaran.index', compact('list_tabel', 'list_periode', 'list_wilayah', 
            'tabel_filter', 'periode_filter', 'wilayah_filter', 'putaran_filter', 'data',
            'tahun_berlaku', 'max_putaran'));
    }
}