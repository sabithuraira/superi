<?php

namespace App\Http\Controllers;

use App\Exports\CreateHasilRekonExport;
use App\Helpers\AssetData;
use App\PdrbFinal;
use App\Rekon;
use App\SettingApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class RekonsiliasiController extends Controller
{
    public $list_wilayah;
    public $tahun_berlaku;
    public $triwulan_berlaku;
    public $list_periode = [];
    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
        $this->tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first()->setting_value;
        $this->triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first()->setting_value;
        for ($t = $this->tahun_berlaku - 3; $t <= $this->tahun_berlaku; $t++) {
            for ($i = 1; $i <= 4; $i++) {
                array_push($this->list_periode, "{$t}Q{$i}");
            }
        }
    }

    public function index(Request $request)
    {
        $list_komponen = AssetData::$list_detail_komponen_rekon;
        $list_periode = $this->list_periode;
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q' . $triwulan_berlaku];
        $komponen_filter = 'c_1a';
        return view('rekonsiliasi.index', compact('list_komponen', 'list_periode', 'komponen_filter', 'periode_filter', 'tahun_berlaku'));
    }

    public function get_data(Request $request)
    {
        $periode_filter = $request->periode_filter;
        $komponen_filter = $request->komponen_filter;
        $list_wilayah = $this->list_wilayah;

        $datas = [];
        foreach ($list_wilayah as $id_wil => $wilayah) {
            $row = [];
            $row['kode_kab'] = $id_wil;
            $row['nama_kab'] = $wilayah;

            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);

                $komponen_filter = trim($komponen_filter); // "c_1a + c_1b"

                $komponen = array_map('trim', explode('+', $komponen_filter));
                // dd($komponen_filter);
                $expr     = implode(' + ', $komponen);
                $expr_adj = implode(
                    ' + ',
                    array_map(fn($c) => $c . '_adj', $komponen)
                );


                $kolom     = DB::raw("($expr) as nilai");
                $kolom_adj = DB::raw("($expr_adj) as nilai_adj");

                $sumkolom     = DB::raw("SUM($expr) as nilai");
                $sumkolom_adj =  DB::raw("SUM($expr_adj) as nilai_adj");

                $adhb = Rekon::select('id', 'kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 1)
                    ->first();

                $adhk = Rekon::select(
                    'id',
                    'kode_kab',
                    'tahun',
                    'q',
                    'adhb_or_adhk',
                    $kolom,
                    $kolom_adj
                )
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 2)
                    ->first();

                // dd($adhk);

                if ($arr_periode[1] != 1) {
                    $tahun_q1 = $arr_periode[0];
                    $q_q1 = $arr_periode[1] - 1;
                } else {
                    $tahun_q1 = $arr_periode[0] - 1;
                    $q_q1 = 4;
                }

                $adhb_q1 = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $tahun_q1)
                    ->where('q', $q_q1)
                    ->where('adhb_or_adhk', 1)
                    ->first();

                $adhk_q1 = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $tahun_q1)
                    ->where('q', $q_q1)
                    ->where('adhb_or_adhk', 2)
                    ->first();

                $adhb_y1 = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 1)
                    ->first();

                $adhk_y1 = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 2)
                    ->first();

                $q_c = [];
                $adhk_c_q = []; // Simpan hasil query untuk tahun sekarang
                $adhk_c1_q = []; // Simpan hasil query untuk tahun sebelumnya

                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q_c[] = $i;

                    $adhk_c_q[$i] = Rekon::select('kode_kab', 'tahun', 'adhb_or_adhk', $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', $id_wil)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', [$i])
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_kab', 'tahun', 'adhb_or_adhk')
                        ->first();

                    $adhk_c1_q[$i] = Rekon::select('kode_kab', 'tahun', 'adhb_or_adhk', $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', $id_wil)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', [$i])
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_kab', 'tahun', 'adhb_or_adhk')
                        ->first();

                    // dd($adhk_c1_q);
                    $row[$periode . '_adhk_c_q'][$i] = $adhk_c_q[$i] ? $adhk_c_q[$i]->nilai : null;
                    $row[$periode . '_adhk_c_q_adj'][$i] = $adhk_c_q[$i] ? $adhk_c_q[$i]->nilai_adj : null;
                    $row[$periode . '_adhk_c1_q'][$i] = $adhk_c1_q[$i] ? $adhk_c1_q[$i]->nilai : null;
                    $row[$periode . '_adhk_c1_q_adj'][$i] = $adhk_c1_q[$i] ? $adhk_c1_q[$i]->nilai_adj : null;
                }


                $adhk_c = Rekon::select('kode_kab', 'tahun', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->wherein('q', $q_c)
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('kode_kab', 'tahun', 'adhb_or_adhk')
                    ->first();
                $adhk_c1 = Rekon::select('kode_kab', 'tahun', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->wherein('q', $q_c)
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('kode_kab', 'tahun', 'adhb_or_adhk')
                    ->first();

                // adhb
                $row[$periode . '_adhb_id'] = $adhb ? $adhb->id : null;
                $row[$periode . '_adhb'] = $adhb ? $adhb->nilai : null;
                $row[$periode . '_adhb_adj'] = $adhb ? $adhb->nilai_adj : null;
                $row[$periode . '_adhb_q1'] = $adhb_q1 ? $adhb_q1->nilai : null;
                $row[$periode . '_adhb_q1_adj'] = $adhb_q1 ? $adhb_q1->nilai_adj : null;
                $row[$periode . '_adhb_y1'] = $adhb_y1 ? $adhb_y1->nilai : null;
                $row[$periode . '_adhb_y1_adj'] = $adhb_y1 ? $adhb_y1->nilai_adj : null;
                // adhk
                $row[$periode . '_adhk_id'] = $adhk ? $adhk->id : null;
                $row[$periode . '_adhk'] = $adhk ? $adhk->nilai : null;
                $row[$periode . '_adhk_adj'] = $adhk ? $adhk->nilai_adj : null;
                $row[$periode . '_adhk_q1'] = $adhk_q1 ? $adhk_q1->nilai : null;
                $row[$periode . '_adhk_q1_adj'] = $adhk_q1 ? $adhk_q1->nilai_adj : null;
                $row[$periode . '_adhk_y1'] = $adhk_y1 ? $adhk_y1->nilai : null;
                $row[$periode . '_adhk_y1_adj'] = $adhk_y1 ? $adhk_y1->nilai_adj : null;
                $row[$periode . '_adhk_c'] = $adhk_c ? $adhk_c->nilai : null;
                $row[$periode . '_adhk_c_adj'] = $adhk_c ? $adhk_c->nilai_adj : null;
                $row[$periode . '_adhk_c1'] = $adhk_c1 ? $adhk_c1->nilai : null;
                $row[$periode . '_adhk_c1_adj'] = $adhk_c1 ? $adhk_c1->nilai_adj : null;
            }

            $datas[] = $row;

            if ($id_wil == "00") {
                $row = [];
                $row['kode_kab'] = "-";
                $row['nama_kab'] = "Total 17 Kabkot";
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    // $kolom = $komponen_filter;
                    // $kolom_adj = $komponen_filter . '_adj';

                    $adhb = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk', $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 1)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    $adhk = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    if ($arr_periode[1] != 1) {
                        $tahun_q1 = $arr_periode[0];
                        $q_q1 = $arr_periode[1] - 1;
                    } else {
                        $tahun_q1 = $arr_periode[0] - 1;
                        $q_q1 = 4;
                    }

                    $adhb_q1 = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $tahun_q1)
                        ->where('q', $q_q1)
                        ->where('adhb_or_adhk', 1)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    $adhk_q1 = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk', $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $tahun_q1)
                        ->where('q', $q_q1)
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    $adhb_y1 = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 1)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    $adhk_y1 = Rekon::select('kode_prov', 'tahun', 'q', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_prov', 'tahun', 'q', 'adhb_or_adhk')
                        ->first();

                    $q_c = [];
                    $adhk_c_q = []; // Simpan hasil query untuk tahun sekarang
                    $adhk_c1_q = []; // Simpan hasil query untuk tahun sebelumnya

                    for ($i = 1; $i <= $arr_periode[1]; $i++) {
                        $q_c[] = $i;

                        $adhk_c_q[$i] = Rekon::select('kode_prov', 'tahun', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                            ->where('kode_kab', '!=', '00')
                            ->where('tahun', $arr_periode[0])
                            ->wherein('q', [$i])
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('kode_prov', 'tahun', 'adhb_or_adhk')
                            ->first();

                        $adhk_c1_q[$i] = Rekon::select('kode_prov', 'tahun', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                            ->where('kode_kab', '!=', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->wherein('q', [$i])
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('kode_prov', 'tahun', 'adhb_or_adhk')
                            ->first();
                        // dd($adhk_c1_q);
                        $row[$periode . '_adhk_c_q'][$i] = $adhk_c_q[$i] ? $adhk_c_q[$i]->nilai : null;
                        $row[$periode . '_adhk_c_q_adj'][$i] = $adhk_c_q[$i] ? $adhk_c_q[$i]->nilai_adj : null;
                        $row[$periode . '_adhk_c1_q'][$i] = $adhk_c1_q[$i] ? $adhk_c1_q[$i]->nilai : null;
                        $row[$periode . '_adhk_c1_q_adj'][$i] = $adhk_c1_q[$i] ? $adhk_c1_q[$i]->nilai_adj : null;
                    }

                    $adhk_c = Rekon::select('kode_prov', 'tahun', 'adhb_or_adhk', $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q_c)
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_prov', 'tahun', 'adhb_or_adhk')
                        ->first();

                    $adhk_c1 = Rekon::select('kode_prov', 'tahun', 'adhb_or_adhk',  $sumkolom, $sumkolom_adj)
                        ->where('kode_kab', '!=', '00')
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q_c)
                        ->where('adhb_or_adhk', 2)
                        ->groupBy('kode_prov', 'tahun', 'adhb_or_adhk')
                        ->first();
                    // dd($adhk_c1);
                    // adhb
                    $row[$periode . '_adhb_id'] = $adhb ? $adhb->id : null;
                    $row[$periode . '_adhb'] = $adhb ? $adhb->nilai : null;
                    $row[$periode . '_adhb_adj'] = $adhb ? $adhb->nilai_adj : null;
                    $row[$periode . '_adhb_q1'] = $adhb_q1 ? $adhb_q1->nilai : null;
                    $row[$periode . '_adhb_q1_adj'] = $adhb_q1 ? $adhb_q1->nilai_adj : null;
                    $row[$periode . '_adhb_y1'] = $adhb_y1 ? $adhb_y1->nilai : null;
                    $row[$periode . '_adhb_y1_adj'] = $adhb_y1 ? $adhb_y1->nilai_adj : null;
                    // adhk
                    $row[$periode . '_adhk_id'] = $adhk ? $adhk->id : null;
                    $row[$periode . '_adhk'] = $adhk ? $adhk->nilai : null;
                    $row[$periode . '_adhk_adj'] = $adhk ? $adhk->nilai_adj : null;
                    $row[$periode . '_adhk_q1'] = $adhk_q1 ? $adhk_q1->nilai : null;
                    $row[$periode . '_adhk_q1_adj'] = $adhk_q1 ? $adhk_q1->nilai_adj : null;
                    $row[$periode . '_adhk_y1'] = $adhk_y1 ? $adhk_y1->nilai : null;
                    $row[$periode . '_adhk_y1_adj'] = $adhk_y1 ? $adhk_y1->nilai_adj : null;
                    $row[$periode . '_adhk_c'] = $adhk_c ? $adhk_c->nilai : null;
                    $row[$periode . '_adhk_c_adj'] = $adhk_c ? $adhk_c->nilai_adj : null;
                    $row[$periode . '_adhk_c1'] = $adhk_c1 ? $adhk_c1->nilai : null;
                    $row[$periode . '_adhk_c1_adj'] = $adhk_c1 ? $adhk_c1->nilai_adj : null;
                }
                $datas[] = $row;

                $row = [];
                $row['kode_kab'] = "d%";
                $row['nama_kab'] = "Diskrepansi(%)";
                foreach ($periode_filter as $periode) {
                    $row[$periode . '_adhb_id'] = $adhb ? $adhb->id : null;

                    $row[$periode . '_adhb'] =
                        ($datas[1][$periode . '_adhb'] ?? 0) && ($datas[0][$periode . '_adhb'] ?? 0) != 0
                        ? ($datas[1][$periode . '_adhb'] / $datas[0][$periode . '_adhb'] * 100) - 100
                        : null;

                    $row[$periode . '_adhb_adj'] =
                        ($datas[1][$periode . '_adhb_adj'] ?? 0) &&
                        ($datas[0][$periode . '_adhb_adj'] ?? 0) &&
                        (($datas[0][$periode . '_adhb'] + $datas[0][$periode . '_adhb_adj']) != 0)
                        ? (($datas[1][$periode . '_adhb'] + $datas[1][$periode . '_adhb_adj'])
                            / ($datas[0][$periode . '_adhb'] + $datas[0][$periode . '_adhb_adj'])
                            * 100) - 100
                        : null;

                    $row[$periode . '_adhk_id'] = $adhk ? $adhk->id : null;

                    $row[$periode . '_adhk'] =
                        ($datas[1][$periode . '_adhk'] ?? 0) && ($datas[0][$periode . '_adhk'] ?? 0) != 0
                        ? ($datas[1][$periode . '_adhk'] / $datas[0][$periode . '_adhk'] * 100) - 100
                        : null;

                    $row[$periode . '_adhk_adj'] =
                        ($datas[1][$periode . '_adhk_adj'] ?? 0) &&
                        ($datas[0][$periode . '_adhk_adj'] ?? 0) &&
                        (($datas[0][$periode . '_adhk'] + $datas[0][$periode . '_adhk_adj']) != 0)
                        ? (($datas[1][$periode . '_adhk'] + $datas[1][$periode . '_adhk_adj'])
                            / ($datas[0][$periode . '_adhk'] + $datas[0][$periode . '_adhk_adj'])
                            * 100) - 100
                        : null;
                }
                $datas[] = $row;

                $row = [];
                $row['kode_kab'] = "d";
                $row['nama_kab'] = "Diskrepansi";
                foreach ($periode_filter as $periode) {
                    $row[$periode . '_adhb_id'] = $adhb ? $adhb->id : null;
                    $row[$periode . '_adhb'] = $datas[1][$periode . '_adhb'] && $datas[0][$periode . '_adhb'] ? $datas[1][$periode . '_adhb'] - $datas[0][$periode . '_adhb'] : null;
                    $row[$periode . '_adhb_adj'] = $datas[1][$periode . '_adhb_adj'] && $datas[0][$periode . '_adhb_adj'] ? $datas[1][$periode . '_adhb_adj'] - $datas[0][$periode . '_adhb_adj'] : null;
                    $row[$periode . '_adhk_id'] = $adhk ? $adhk->id : null;
                    $row[$periode . '_adhk'] = $datas[1][$periode . '_adhk'] && $datas[0][$periode . '_adhk'] ? $datas[1][$periode . '_adhk'] - $datas[0][$periode . '_adhk'] : null;
                    $row[$periode . '_adhk_adj'] = $datas[1][$periode . '_adhk_adj'] && $datas[0][$periode . '_adhk_adj'] ? $datas[1][$periode . '_adhk_adj'] - $datas[0][$periode . '_adhk_adj'] : null;
                }
                $datas[] = $row;
            }
        }
        $moved = [$datas[2], $datas[3]];
        $remaining = array_filter($datas, function ($v, $i) {
            return !in_array($i, [2, 3]);
        }, ARRAY_FILTER_USE_BOTH);

        $newData = array_values(array_merge($moved, $remaining));
        return response()->json([
            'success' => '1',
            'data' => $newData,
        ]);
    }

    public function save_data(Request $request)
    {
        $data = $request->data;
        // dd($data);
        foreach ($data as $dt) {
            $columnName = $dt['komp_id'] . '_adj';
            $model = Rekon::find($dt['id']);

            if ($model) {
                // dd($columnName);
                $model->{$columnName} = strval($dt['value']);
                // var_dump($dt['value'], $model->{$columnName});
                $c_1a_adj = $model->c_1a_adj ? $model->c_1a_adj : 0;
                $c_1b_adj = $model->c_1b_adj ? $model->c_1b_adj : 0;
                $c_1c_adj = $model->c_1c_adj ? $model->c_1c_adj : 0;
                $c_1d_adj = $model->c_1d_adj ? $model->c_1d_adj : 0;
                $c_1e_adj = $model->c_1e_adj ? $model->c_1e_adj : 0;
                $c_1f_adj = $model->c_1f_adj ? $model->c_1f_adj : 0;
                $c_1g_adj = $model->c_1g_adj ? $model->c_1g_adj : 0;
                $c_1h_adj = $model->c_1h_adj ? $model->c_1h_adj : 0;
                $c_1i_adj = $model->c_1i_adj ? $model->c_1i_adj : 0;
                $c_1j_adj = $model->c_1j_adj ? $model->c_1j_adj : 0;
                $c_1k_adj = $model->c_1k_adj ? $model->c_1k_adj : 0;
                $c_1l_adj = $model->c_1l_adj ? $model->c_1l_adj : 0;
                $c_2_adj = $model->c_2_adj ? $model->c_2_adj : 0;
                $c_3_adj = $model->c_3_adj ? $model->c_3_adj : 0;
                $c_4a_adj = $model->c_4a_adj ? $model->c_4a_adj : 0;
                $c_4b_adj = $model->c_4b_adj ? $model->c_4b_adj : 0;
                $c_5_adj = $model->c_5_adj ? $model->c_5_adj : 0;
                // $c_6_adj = $model->c_1a_adj ? $model->c_1a_adj : 0;
                $c_7_adj = $model->c_7_adj ? $model->c_7_adj : 0;

                $model->c_6_adj = ($c_1a_adj + $c_1b_adj + $c_1c_adj + $c_1d_adj + $c_1e_adj + $c_1f_adj + $c_1g_adj + $c_1h_adj + $c_1i_adj + $c_1j_adj + $c_1k_adj + $c_1l_adj
                    + $c_2_adj + $c_3_adj + $c_4a_adj + $c_4b_adj + $c_5_adj - $c_7_adj) * -1;
                $model->save();
            }
        }

        if ($data) {
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Menyimpan data / Data tidak ditemukan',
            ]);
        }
    }


    public function sync_data(Request $request)
    {
        $periode_filter = '2025Q1';
        if ($request->periode_filter) {
            $periode_filter = $request->periode_filter;
        }
        $arr_periode = explode("Q", $periode_filter);

        $pdrb_finals = PdrbFinal::where('tahun', $arr_periode[0])->get();
        $successCount = 0;
        foreach ($pdrb_finals as $pdrb_final) {
            $rekonData = [
                'id' => $pdrb_final->id,
                'tahun' => $pdrb_final->tahun,
                'q' => $pdrb_final->q,
                'kode_kab' => $pdrb_final->kode_kab,
                'kode_prov' => $pdrb_final->kode_prov,
                'revisi_ke' => $pdrb_final->revisi_ke,
                'putaran' => $pdrb_final->putaran,
                'upload_tahun' => $pdrb_final->upload_tahun,
                'upload_q' => $pdrb_final->upload_q,
                'adhb_or_adhk' => $pdrb_final->adhb_or_adhk,
                'status_data' => $pdrb_final->status_data,
                'c_1' => $pdrb_final->c_1,
                'c_1a' => $pdrb_final->c_1a,
                'c_1a_adj' => null,
                'c_1b' => $pdrb_final->c_1b,
                'c_1b_adj' => null,
                'c_1c' => $pdrb_final->c_1c,
                'c_1c_adj' => null,
                'c_1d' => $pdrb_final->c_1d,
                'c_1d_adj' => null,
                'c_1e' => $pdrb_final->c_1e,
                'c_1e_adj' => null,
                'c_1f' => $pdrb_final->c_1f,
                'c_1f_adj' => null,
                'c_1g' => $pdrb_final->c_1g,
                'c_1g_adj' => null,
                'c_1h' => $pdrb_final->c_1h,
                'c_1h_adj' => null,
                'c_1i' => $pdrb_final->c_1i,
                'c_1i_adj' => null,
                'c_1j' => $pdrb_final->c_1j,
                'c_1j_adj' => null,
                'c_1k' => $pdrb_final->c_1k,
                'c_1k_adj' => null,
                'c_1l' => $pdrb_final->c_1l,
                'c_1l_adj' => null,
                'c_2' => $pdrb_final->c_2,
                'c_2_adj' => null,
                'c_3' => $pdrb_final->c_3,
                'c_3_adj' => null,
                'c_4' => $pdrb_final->c_4,
                'c_4a' => $pdrb_final->c_4a,
                'c_4a_adj' => null,
                'c_4b' => $pdrb_final->c_4b,
                'c_4b_adj' => null,
                'c_5' => $pdrb_final->c_5,
                'c_5_adj' => null,
                'c_6' => $pdrb_final->c_6,
                'c_6_adj' => null,
                'c_7' => $pdrb_final->c_7,
                'c_7_adj' => null,
                'c_pdrb' => $pdrb_final->c_pdrb,
                'ketua_tim_id' => $pdrb_final->ketua_tim_id,
                'pimpinan_id' => $pdrb_final->pimpinan_id,
                'created_by' => $pdrb_final->created_by,
                'updated_by' => $pdrb_final->updated_by,
                'created_at' => now(),
                'updated_at' => now()
            ];
            Rekon::create($rekonData);
            $successCount++;
        }
        return response()->json([
            'success' => true,
            'message' => 'Data tahun ' . $arr_periode[0] . ' berhasil disimpan (' . $successCount . ' record)',
        ]);
    }

    public function create_hasil_rekon(Request $request)
    {
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q' . $triwulan_berlaku];
        $kabkots = config("app.wilayah");

        $zipFile = storage_path('app/rekon_all_kabkot.zip');
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // dd($periode_filter);
        foreach ($kabkots as $kode => $kabkot) {

            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);

                $adhb = Rekon::where('kode_kab', $kode)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 1)
                    ->first();
                $adhk = Rekon::where('kode_kab', $kode)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 2)
                    ->first();

                $periode_data[$periode] = [
                    'adhb' => $adhb,
                    'adhk' => $adhk
                ];
            }

            $tempFileName = "rekon_temp_{$kode}.xlsx";
            $tempFilePath = storage_path("app/{$tempFileName}");

            // ✔ Generate file Excel ke storage/app/
            Excel::store(new CreateHasilRekonExport($periode_data), $tempFileName, null, \Maatwebsite\Excel\Excel::XLSX);

            // ✔ Masukkan ke ZIP
            $zip->addFile($tempFilePath, "16{$kode}_hasil_rekon.xlsx");
        }
        $zip->close();
        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
}
