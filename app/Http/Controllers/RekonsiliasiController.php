<?php

namespace App\Http\Controllers;

use App\Helpers\AssetData;
use App\PdrbFinal;
use App\Rekon;
use App\SettingApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $kolom = $komponen_filter;
                $kolom_adj = $komponen_filter . '_adj';

                $adhb = Rekon::select('id', 'kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 1)
                    ->first();

                $adhk = Rekon::select('id', 'kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])
                    ->where('adhb_or_adhk', 2)
                    ->first();

                if ($arr_periode[1] != 1) {
                    $tahun_q1 = $arr_periode[0];
                    $q_q1 = $arr_periode[1] - 1;
                } else {
                    $tahun_q1 = $arr_periode[0] - 1;
                    $q_q1 = 4;
                }

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
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q_c[] = $i;
                }

                $adhk_c = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0])
                    ->wherein('q', $q_c)
                    ->where('adhb_or_adhk', 2)
                    ->first();

                $adhk_c1 = Rekon::select('kode_kab', 'tahun', 'q', 'adhb_or_adhk', $kolom, $kolom_adj)
                    ->where('kode_kab', $id_wil)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->wherein('q', $q_c)
                    ->where('adhb_or_adhk', 2)
                    ->first();

                $row[$periode . '_adhb_id'] = $adhb ? $adhb->id : null;
                $row[$periode . '_adhb'] = $adhb ? $adhb->$kolom : null;
                $row[$periode . '_adhb_adj'] = $adhb ? $adhb->$kolom_adj : null;
                $row[$periode . '_adhb_y1'] = $adhb_y1 ? $adhb_y1->$kolom : null;
                $row[$periode . '_adhk_id'] = $adhk ? $adhk->id : null;
                $row[$periode . '_adhk'] = $adhk ? $adhk->$kolom : null;
                $row[$periode . '_adhk_adj'] = $adhk ? $adhk->$kolom_adj : null;
                $row[$periode . '_adhk_q1'] = $adhk_q1 ? $adhk_q1->$kolom : null;
                $row[$periode . '_adhk_y1'] = $adhk_y1 ? $adhk_y1->$kolom : null;
                $row[$periode . '_adhk_c'] = $adhk_c ? $adhk_c->$kolom : null;
                $row[$periode . '_adhk_c1'] = $adhk_c1 ? $adhk_c1->$kolom : null;
            }

            $datas[] = $row;
        }

        return response()->json([
            'success' => '1',
            'data' => $datas,
        ]);
    }

    public function save_data(Request $request)
    {

        // dd($request->data);
        $data = $request->data;
        foreach ($data as $dt) {
            $columnName = $dt['komp_id'] . '_adj';
            $model = Rekon::find($dt['id']);
            if ($model) {
                $model->{$columnName} = $dt['value'];
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
}
