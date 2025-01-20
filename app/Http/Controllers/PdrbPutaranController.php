<?php

namespace App\Http\Controllers;

use App\Komponen;
use App\Pdrb;
use App\PdrbFinal;
use Illuminate\Http\Request;

class PdrbPutaranController extends Controller
{
    //
    public $list_wilayah;

    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
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

    public $list_periode = [
        '2024Q1', '2024Q2', '2024Q3', '2024Q4'
    ];

    public function index(Request $request, $id){
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_detail_komponen = Komponen::where('status_aktif', 1)
                                    ->orderby('no_komponen')->get();
        $list_wilayah = $this->list_wilayah;
        $tabel_filter = $id; //$request->tabel_filter ? $request->tabel_filter : '3.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : $list_periode;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';
        $putaran_filter = $request->putaran_filter ? $request->putaran_filter : '1';

        $adhb_or_adhk = ($id === '3.1') ? 1 : 2;
        $data = [];
        
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
                    $condition_arr[] = ['status_data', 1];
    
                    $data_y = PdrbFinal::where($condition_arr)
                        ->orderby('revisi_ke', 'desc')
                        ->first();
                    $row[$periode] = $data_y ? $data_y->$komp_id : null;
                }
            }
            $data[] = $row;
            
        }

        /////////
        // if ($id === '3.1') {
        //     foreach ($list_detail_komponen as $komponen) {
        //         $row = [];
        //         $row = [
        //             'id' => $komponen['no_komponen'],
        //             'name' => $komponen['nama_komponen'],
        //         ];
        //         $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
        //         foreach ($periode_filter as $periode) {
        //             $arr_periode = explode("Q", $periode);

        //             $data_y = Pdrb::where('kode_kab', $wilayah_filter)
        //                 ->where('tahun', $arr_periode[0])
        //                 ->where('q', $arr_periode[1])
        //                 ->where('adhb_or_adhk', 1)
        //                 ->where('status_data', 1)
        //                 ->orderby('revisi_ke', 'desc')
        //                 ->first();
        //             $row[$periode] = $data_y ? $data_y->$komp_id : null;
        //         }
        //         $data[] = $row;
        //     }
        // } else if ($id === '3.2') {
        //     foreach ($list_detail_komponen as $komponen) {
        //         $row = [];
        //         $row = [
        //             'id' => $komponen['no_komponen'],
        //             'name' => $komponen['nama_komponen'],
        //         ];
        //         $komp_id = $komponen['no_komponen'];
        //         foreach ($periode_filter as $periode) {
        //             $arr_periode = explode("Q", $periode);
        //             $data_y = Pdrb::where('kode_kab', $wilayah_filter)
        //                 ->where('tahun', $arr_periode[0])
        //                 ->where('q', $arr_periode[1])
        //                 ->where('adhb_or_adhk', 2)
        //                 ->where('status_data', 1)
        //                 ->orderby('revisi_ke', 'desc')
        //                 ->first();
        //             $row[$periode] = $data_y ? $data_y->$komp_id : null;
        //         }
        //         $data[] = $row;
        //     }
        // }
        return view('pdrb_putaran.index', compact('list_tabel', 'list_periode', 'list_wilayah', 
            'tabel_filter', 'periode_filter', 'wilayah_filter', 'putaran_filter', 'data'));
    }
}
