<?php

namespace App\Http\Controllers;

use App\Helpers\AssetData;
use App\Pdrb;
use App\PdrbFinal;
use App\SettingApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SimulasiController extends Controller
{
    public $list_wilayah;
    public $tahun_berlaku;
    public $triwulan_berlaku;
    public $list_periode = [];
    public $list_tabel = [
        [
            'id' => '1',
            'name' => 'PDB ADHB',
        ],
        [
            'id' => '2',
            'name' => 'PDB ADHK',
        ],
        [
            'id' => '3',
            'name' => 'Distribusi ADHB',
        ],
        [
            'id' => '4',
            'name' => 'Distribusi ADHK',
        ],
        [
            'id' => '5',
            'name' => 'Laju Pertumbuhan Q To Q',
        ],
        [
            'id' => '6',
            'name' => 'Laju Pertumbuhan Y on Y',
        ],
        [
            'id' => '7',
            'name' => 'Laju Pertumbuhan Kumulatif',
        ],
        [
            'id' => '8',
            'name' => 'Laju Implisit',
        ],
        [
            'id' => '9',
            'name' => 'Laju Pertumbuhan Indeks Implisit Q-to-Q',
        ],
        [
            'id' => '10',
            'name' => 'Laju Pertumbuhan Indeks Implisit Y-on-Y',
        ],
        [
            'id' => '11',
            'name' => 'Laju Pertumbuhan Indeks Implisit C-to-C',
        ],
        [
            'id' => '12',
            'name' => 'Sumber Pertumbuhan Q-to-Q',
        ],
        [
            'id' => '13',
            'name' => 'Sumber Pertumbuhan Y-on-Y',
        ],
        [
            'id' => '14',
            'name' => 'Sumber Pertumbuhan C-to-C',
        ],
    ];

    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
        $this->tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first()->setting_value;
        $this->triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first()->setting_value;
        for ($t = $this->tahun_berlaku - 3; $t <= $this->tahun_berlaku; $t++) {
            for ($i = 1; $i <= 4; $i++) {
                array_push($this->list_periode, "{$t}Q{$i}");
            }
            array_push($this->list_periode, "{$t}");
        }
    }


    public function index(Request $request)
    {
        $list_komponen = AssetData::$list_detail_komponen_12_pkrt;
        $list_periode = $this->list_periode;
        $list_tabel = $this->list_tabel;
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q' . $triwulan_berlaku];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter
            : array_map(function ($item) {
                return $item['id'];
            }, $list_komponen);
        $array_komp_filter = [];

        foreach ($komponen_filter as $item) {
            $array_komp_filter = array_merge($array_komp_filter, array_map('trim', explode(',', $item)));
        }

        $komponens = [];
        return view('rekonsiliasi.simulasi', compact('list_komponen', 'list_periode', 'list_tabel', 'komponen_filter', 'periode_filter', 'tahun_berlaku', 'tabel_filter'));
    }

    public function get_data(Request $request)
    {
        $tahun =  $this->tahun_berlaku;
        $triwulan = $this->triwulan_berlaku;
        $auth = Auth::user();
        $list_komponen = AssetData::$list_detail_komponen_12_pkrt;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter :  [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun . 'Q' . $triwulan];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : $auth->kdkab;

        $list_tabel = $tabel_filter;
        $list_periode = $periode_filter;
        $tabels = $this->list_tabel;
        $datas = [];

        foreach ($list_tabel as $tabel) {
            $data_periode = [];
            $name = "";

            foreach ($tabels as $tbl) {
                if ($tbl['id'] == $tabel) {
                    $name = $tbl['name'];
                }
            }


            $data = [];
            foreach ($komponen_filter as $komponen) {
                $nama_komponen = "";
                $row = [];
                foreach ($list_komponen as $kmp) {
                    if ($kmp['id'] === $komponen) {
                        $nama_komponen = $kmp['alias'];
                    }
                }
                $row['id'] = $komponen;
                $row['name'] = $nama_komponen;
                // foreach ($data_periode as $periode => $dt_periode) {
                //     $row[$periode] = $dt_periode[$komponen];
                // }

                // foreach ($list_periode as $periode) {
                $dt = $this->rumus($tabel, $auth->kdkab, $list_periode);


                // if ($dt) {
                //     $data_periode[$periode] = $dt;
                // }
                // }
                $data[] = $row;
            }
            $datas[] = [
                'id' => $tabel,
                'name' => $name,
                'data' => $data
            ];
        }

        return response()->json([
            'success' => '1',
            'data' => $datas,
        ]);
    }


    public function rumus($id, $id_wil, $list_periode)
    {
        $list_detail_komponen = AssetData::$list_detail_komponen_12_pkrt;
        $str_sql_select = '';
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= 'SUM(' . $item['id'] . ') as ' . $item['id'] . ', ';
        }
        $str_sql_select = substr($str_sql_select, 0, -2);

        $data = [];
        foreach ($list_periode as $periode) {
            $tahun = "";
            $q = [1, 2, 3, 4];
            $arr_periode = explode('Q', $periode);
            $tahun = $arr_periode[0];
            if (sizeof($arr_periode) > 1) {
                $q = [$arr_periode[1]];
            }

            if ($id == 1) {
                $dt =  PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $tahun)
                    ->wherein('q', $q)
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('kode_kab')
                    ->first();
                $data[] = $dt;
            } elseif ($id == 2) {
                $dt =  PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $tahun)
                    ->wherein('q', $q)
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('kode_kab')
                    ->first();
                $data[] = $dt;
            }
        }
        return $data;
    }

    public function get_data_simulasi(Request $request)
    {
        $tahun =  $this->tahun_berlaku;
        $triwulan = $this->triwulan_berlaku;
        $auth = Auth::user();
        $list_komponen = AssetData::$list_detail_komponen_12_pkrt;
        $str_sql_select = '';
        foreach ($list_komponen as $item) {
            $str_sql_select .= 'SUM(' . $item['id'] . ') as ' . $item['id'] . ', ';
        }
        $str_sql_select = substr($str_sql_select, 0, -2);

        $d_y = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('tahun', $tahun)
            ->wherein('q', [$triwulan])
            ->where('adhb_or_adhk', 1)
            ->where('kode_kab', $auth->kdkab)
            ->groupBy('kode_kab')
            ->first();

        if (!$d_y) {
            return response()->json([
                'success' => '0',
                'message' => 'belum ada data'
            ]);
        }
        if ($triwulan == 1) {
            $q_t = 4;
            $y_t = $tahun - 1;
        } else {
            $q_t = $triwulan - 1;
            $y_t = $tahun;
        }


        $d_q = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('tahun', $y_t)
            ->wherein('q', [$q_t])
            ->where('adhb_or_adhk', 1)
            ->where('kode_kab', $auth->kdkab)
            ->groupBy('kode_kab')
            ->first();
        $d_y1 = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('tahun', $tahun - 1)
            ->wherein('q', [$triwulan])
            ->where('adhb_or_adhk', 1)
            ->where('kode_kab', $auth->kdkab)
            ->groupBy('kode_kab')
            ->first();


        $q_c = [];
        for ($i = 1; $i <= $triwulan; $i++) {
            $q_c[] = $i;
        }
        $d_c = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('tahun', $tahun)
            ->wherein('q', $q_c)
            ->where('adhb_or_adhk', 1)
            ->where('kode_kab', $auth->kdkab)
            ->groupBy('kode_kab')
            ->first();
        $d_c1 = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('tahun', $tahun - 1)
            ->wherein('q', $q_c)
            ->where('adhb_or_adhk', 1)
            ->where('kode_kab', $auth->kdkab)
            ->groupBy('kode_kab')
            ->first();

        $datas = [];
        foreach ($list_komponen as $komponen) {
            $komp_id = $komponen['id'];
            $row['id'] =   $komp_id;
            $row['name'] = $komponen['alias'];
            $row['distribusi'] = $d_y && $d_y->c_pdrb ? $d_y->$komp_id / $d_y->c_pdrb * 100 : null;
            $row['qtq'] = $d_q && $d_q->$komp_id ? ($d_y->$komp_id - $d_q->$komp_id) / $d_q->$komp_id * 100 : null;
            $row['yty'] = $d_y1 && $d_y1->$komp_id ? ($d_y->$komp_id - $d_y1->$komp_id) / $d_y1->$komp_id * 100 : null;
            $row['ctc'] = $d_c1 && $d_c1->$komp_id ? ($d_c->$komp_id - $d_c1->$komp_id) / $d_c1->$komp_id * 100 : null;
            $datas[] = $row;
        }


        return response()->json([
            'success' => '1',
            'data' => $datas,
        ]);
    }
}
