<?php

namespace App\Http\Controllers;

use App\Exports\RingkasanExportAll;
use App\Pdrb;
use App\PdrbFinal;
use App\SettingApp;
use App\Helpers\AssetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class TabelRingkasanController extends Controller
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

    public $list_tabel = [];

    public function setListTable()
    {
        if (Auth::user()->kdkab != '00') {
            $this->list_tabel = [
                [
                    'id' => '1.11',
                    'name' => 'Tabel 1.11. Perbandingan Diskrepansi Provinsi dan Kabupaten / Kota Menurut Komponen',
                    'url' => 'pdrb_ringkasan4'
                ],
                [
                    'id' => '1.12',
                    'name' => 'Tabel 1.12. Perbandingan Diskrepansi Kumulatif Nasional dan Regional Menurut Komponen ',
                    'url' => 'pdrb_ringkasan4'
                ],
                [
                    'id' => '1.13',
                    'name' => 'Tabel 1.13. Ringkasan Pertumbuhan Ekstrem Kabupaten Kota',
                    'url' => 'pdrb_ringkasan5'
                ],
                [
                    'id' => '1.14',
                    'name' => 'Tabel 1.14. Ringkasan Revisi Pertumbuhan Ekstrem dan Balik Arah  Kabupaten Kota ',
                    'url' => 'pdrb_ringkasan6'
                ],
            ];
        } else {
            $this->list_tabel = [
                [
                    'id' => '1.1',
                    'name' => 'Tabel 1.1. Perbandingan Pertumbuhan Ekonomi Provinsi dan Kabupaten/Kota Menurut Komponen',
                    'url' => 'pdrb_ringkasan1'
                ],
                [
                    'id' => '1.2',
                    'name' => 'Tabel 1.2. Perbandingan Pertumbuhan Implisit Provinsi dan Kabupaten Kota Menurut Komponen',
                    'url' => 'pdrb_ringkasan1'
                ],
                [
                    'id' => '1.3',
                    'name' => 'Tabel 1.3. Ringkasan Pertumbuhan Ekonomi Kabupaten Kota',
                    'url' => 'pdrb_ringkasan2'
                ],
                [
                    'id' => '1.4',
                    'name' => 'Tabel 1.4. Pertumbuhan Ekonomi (Y-on-Y) Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.5',
                    'name' => 'Tabel 1.5. Pertumbuhan Ekonomi (Q-to-Q) Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.6',
                    'name' => 'Tabel 1.6. Pertumbuhan Ekonomi (C-to-C) Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.7',
                    'name' => 'Tabel 1.7. Distribusi Berlaku Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.8',
                    'name' => 'Tabel 1.8. Distribusi Konstan Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.9',
                    'name' => 'Tabel 1.9. Pertumbuhan Implisit (Y-on-Y) Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.10',
                    'name' => 'Tabel 1.10. Pertumbuhan Implisit (Q-to-Q) Kabupaten Kota ',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.11',
                    'name' => 'Tabel 1.11. Perbandingan Diskrepansi Provinsi dan Kabupaten / Kota Menurut Komponen',
                    'url' => 'pdrb_ringkasan4'
                ],
                [
                    'id' => '1.12',
                    'name' => 'Tabel 1.12. Perbandingan Diskrepansi Kumulatif Nasional dan Regional Menurut Komponen ',
                    'url' => 'pdrb_ringkasan4'
                ],
                [
                    'id' => '1.13',
                    'name' => 'Tabel 1.13. Ringkasan Pertumbuhan Ekstrem Kabupaten Kota',
                    'url' => 'pdrb_ringkasan5'
                ],
                [
                    'id' => '1.14',
                    'name' => 'Tabel 1.14. Ringkasan Revisi Pertumbuhan Ekstrem dan Balik Arah  Kabupaten Kota ',
                    'url' => 'pdrb_ringkasan6'
                ],
                [
                    'id' => '1.15',
                    'name' => 'Tabel 1.15. PDRB ADHB Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
                [
                    'id' => '1.16',
                    'name' => 'Tabel 1.16. PDRB ADHK Per Komponen/Sub Komponen Kabupaten Kota',
                    'url' => 'pdrb_ringkasan3'
                ],
            ];
        }
    }

    public function ringkasan1(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $tahun_berlaku = $this->tahun_berlaku;
        $list_group_komponen = AssetData::getGroupKomponen();
        $list_detail_komponen = AssetData::getDetailKomponen();
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter :  array_map(function ($item) {
            return $item['column'];
        }, $list_group_komponen);
        $array_komp_filter = [];
        foreach ($komponen_filter as $item) {
            $array_komp_filter = array_merge($array_komp_filter, array_map('trim', explode(',', $item)));
        }
        $komponens = [];

        foreach ($array_komp_filter as $arr_komp_filter) {
            foreach ($list_detail_komponen as $dtl_komp) {
                if ($dtl_komp['id'] == $arr_komp_filter) {
                    $komponens[] = [
                        'id' => $arr_komp_filter,
                        'name' => $dtl_komp['name'],
                        'alias' => $dtl_komp['alias']
                    ];
                }
            }
        }
        $data = $this->rumus_1($komponens, $periode_filter, $id);
        return view('pdrb_ringkasan.ringkasan1', compact('list_tabel', 'list_periode', 'tahun_berlaku', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'komponen_filter', 'data'));
    }

    public function ringkasan2(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_wilayah = $this->list_wilayah;


        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.3';
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : $tahun_berlaku . "Q" . $triwulan_berlaku;

        $arr_periode = explode("Q", $periode_filter);

        $data_total_kabkot = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 2, 1);
        $data = $this->rumus_2($list_wilayah, $periode_filter);
        return view('pdrb_ringkasan.ringkasan2', compact('list_tabel', 'list_periode', 'periode_filter', 'tabel_filter', 'data', 'data_total_kabkot'));
    }

    public function ringkasan3(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = AssetData::getGroupKomponen();
        $list_detail_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.4';
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : $tahun_berlaku . "Q" . $triwulan_berlaku;
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1', 'c_1a', 'c_1b', 'c_1c', 'c_1d', 'c_1e', 'c_1f', 'c_1g', 'c_2', 'c_3', 'c_4', 'c_4a', 'c_4b', 'c_5', 'c_6', 'c_7', 'c_pdrb'];
        $komponens = [];
        foreach ($komponen_filter as $komp_filter) {
            foreach ($list_detail_komponen as $dtl_komp) {
                if ($dtl_komp['id'] == $komp_filter) {
                    $komponens[] = [
                        'id' => $komp_filter,
                        'name' => $dtl_komp['name'],
                        'alias' => $dtl_komp['alias']
                    ];
                }
            }
        }


        $data = $this->rumus_3($list_wilayah, $komponens, $periode_filter, $id);
        return view('pdrb_ringkasan.ringkasan3', compact('list_tabel', 'list_periode', 'list_detail_komponen', 'komponen_filter', 'komponens', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan4(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = AssetData::getGroupKomponen();
        $list_detail_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.11';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter :  array_map(function ($item) {
            return $item['column'];
        }, $list_group_komponen);
        $array_komp_filter = [];
        foreach ($komponen_filter as $item) {
            $array_komp_filter = array_merge($array_komp_filter, array_map('trim', explode(',', $item)));
        }
        $komponens = [];
        foreach ($array_komp_filter as $arr_komp_filter) {
            foreach ($list_detail_komponen as $dtl_komp) {
                if ($dtl_komp['id'] == $arr_komp_filter) {
                    $komponens[] = [
                        'id' => $arr_komp_filter,
                        'name' => $dtl_komp['name'],
                        'alias' => $dtl_komp['alias']
                    ];
                }
            }
        }

        $data = $this->rumus_4($komponens, $periode_filter, $id);
        return view('pdrb_ringkasan.ringkasan4', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_detail_komponen', 'list_group_komponen', 'komponen_filter', 'komponens', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan5(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_detail_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.13';
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : $tahun_berlaku . "Q" . $triwulan_berlaku;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = $this->rumus_5($list_detail_komponen, $wilayah_filter, $periode_filter);
        return view('pdrb_ringkasan.ringkasan5', compact('list_tabel', 'list_periode', 'list_wilayah', 'wilayah_filter', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan6(Request $request, $id)
    {
        $this->setListTable();
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_detail_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.14';
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : $tahun_berlaku . "Q" . $triwulan_berlaku;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = $this->rumus_6($list_detail_komponen, $wilayah_filter, $periode_filter);
        return view('pdrb_ringkasan.ringkasan6', compact('list_tabel', 'list_periode', 'list_wilayah', 'wilayah_filter', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function export_all(Request $request)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $tahun_berlaku = $this->tahun_berlaku;
        $triwulan_berlaku = $this->triwulan_berlaku;
        $list_group_komponen = AssetData::getGroupKomponen();
        $list_detail_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $komponens = [];
        foreach ($list_detail_komponen as $dtl_komp) {
            $komponens[] = [
                'id' => $dtl_komp['id'],
                'name' => $dtl_komp['name'],
                'alias' => $dtl_komp['alias']
            ];
        }

        $table = [];
        foreach ($list_tabel as $tbl) {
            $row = [
                'id' => $tbl['id'],
                'name' => $tbl['name']
            ];
            if (in_array($tbl['id'], ['1.1', '1.2'], true)) {
                $periode = is_array($request->periode_filter) && !empty($request->periode_filter)
                    ? $request->periode_filter : $this->list_periode;
                $row['periode_filter'] = $periode;
                $rumus = $this->rumus_1($komponens, $periode, $tbl['id']);
                $row['data'] = $rumus;
            } elseif (in_array($tbl['id'], ['1.3'], true)) {
                $periode = $request->periode_filter && !is_array($request->periode_filter)
                    ? $request->periode_filter : $tahun_berlaku . 'Q' . $triwulan_berlaku;
                $row['periode_filter'] = $periode;
                $rumus = $this->rumus_2($list_wilayah, $periode);
                $row['data'] = $rumus;
            } elseif (in_array($tbl['id'], ['1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '1.10', '1.15', '1.16'], true)) {
                $periode = $request->periode_filter && !is_array($request->periode_filter)
                    ? $request->periode_filter : $tahun_berlaku . 'Q' . $triwulan_berlaku;
                $row['periode_filter'] = $periode;
                $triwulan_berlaku;
                $row['komponens'] = AssetData::getDetailKomponen();
                $rumus = $this->rumus_3($list_wilayah, $komponens, $periode, $tbl['id']);
                $row['data'] = $rumus;
            } elseif (in_array($tbl['id'], ['1.11', '1.12'], true)) {
                $periode = is_array($request->periode_filter) && !empty($request->periode_filter)
                    ? $request->periode_filter : $this->list_periode;
                $row['periode_filter'] = $periode;
                $rumus = $this->rumus_4($komponens,  $periode, $tbl['id']);
                $row['data'] = $rumus;
            } elseif (in_array($tbl['id'], ['1.13'], true)) {
                $row['wilayah_filter'] = $wilayah_filter;
                $periode = $request->periode_filter && !is_array($request->periode_filter)
                    ? $request->periode_filter : $tahun_berlaku . 'Q' . $triwulan_berlaku;
                $row['periode_filter'] = $periode;
                $rumus = $this->rumus_5($komponens, $wilayah_filter, $periode);
                $row['data'] = $rumus;
            } elseif (in_array($tbl['id'], ['1.14'], true)) {
                $row['wilayah_filter'] = $wilayah_filter;
                $periode = $request->periode_filter && !is_array($request->periode_filter)
                    ? $request->periode_filter : $tahun_berlaku . 'Q' . $triwulan_berlaku;
                $row['periode_filter'] = $periode;
                $rumus = $this->rumus_6($komponens, $wilayah_filter, $periode);
                $row['data'] = $rumus;
            }
            $table[] = $row;
        }

        // return view('pdrb_ringkasan.export_all', compact('table'));
        return Excel::download(new RingkasanExportAll($table), 'All_Ringkasan.xlsx');
    }

    /**
     * Get Last Revision Data Base On Kab, Tahun & Q
     */
    // public function get_rev($diskre, $kab, $thn, $q, $adhk, $status)
    // {
    //     // $diskrepansi_prov itu 0 / 1
    //     // jika 0 maka diskrepansi dimana kode_kab ==
    //     // jika 1 maka diskrepansi dimana kode_kab !=
    //     $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
    //         ->when($diskre == 0, function ($query) use ($kab) {
    //             return $query->where('kode_kab', '=', $kab);
    //         }, function ($query) use ($kab) {
    //             return $query->where('kode_kab', '!=', $kab);
    //         })
    //         ->where('tahun', $thn)
    //         ->where('q', "LIKE", '%' . $q . '%')
    //         ->where('adhb_or_adhk', $adhk)
    //         ->where('status_data', "LIKE", '%' . $status . '%')
    //         ->groupBy('kode_kab', 'q')
    //         ->get();

    //     return $rev;
    // }

    /**
     * GET Data with report format
     * Only Get last revisi_ke data
     */
    public function get_data($kab, $thn, $q, $adhk, $status)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= $item['select_id'] . " as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);

        $data = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('kode_kab', $kab)
            ->where('tahun', $thn)
            ->where('q', "LIKE", '%' . $q . '%')
            ->where('adhb_or_adhk', $adhk)
            ->wherein('status_data', [2, 3])
            // ->orderBy('revisi_ke', 'desc')
            ->first();
        return $data;
    }

    /**
     * Get cumulative data based on $diskre, $kab, $thn and $q
     * Each data will check and get data from the last revision
     */
    public function get_data_cumulative($diskre, $kab, $thn, $q, $adhk, $status)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);

        $data = PdrbFinal::select('kode_prov', DB::raw($str_sql_select))
            ->when($diskre == 0, function ($query) use ($kab) {
                return $query->where('kode_kab', '=', $kab);
            }, function ($query) use ($kab) {
                return $query->where('kode_kab', '!=', $kab);
            })
            ->where('tahun', $thn)
            ->wherein('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', ">=", 2)
            ->groupBy('kode_prov')
            ->first();
        return $data;
    }


    public function get_q($kd_kab, $thn, $adhk, $status_rilis)
    {
        $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
            ->where('kode_kab', $kd_kab)
            ->where('tahun', $thn)
            ->where('adhb_or_adhk', $adhk)
            // ->where('status_data', "LIKE", '%' . $status_rilis . '%')
            ->whereIn('status_data', $status_rilis)
            ->groupBy('kode_kab', 'q')
            ->get();
        return $rev;
    }

    public function get_data_cum($kd_kab, $thn, $q, $adhk, $status_rilis, $rev)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);
        $data = Pdrb::select('kode_kab', DB::raw($str_sql_select))
            ->where('kode_kab', $kd_kab)
            ->where('tahun', $thn)
            ->wherein('q', $q)
            ->where('adhb_or_adhk', $adhk)
            // ->where('status_data', "LIKE", '%' . $status_rilis . '%')
            ->where(function ($query) use ($rev) {
                foreach ($rev as $r) {
                    $query->orWhere(function ($subquery) use ($r) {
                        $subquery->where('kode_kab', $r->kode_kab)
                            ->where('q', $r->q)
                            ->where('revisi_ke', $r->max_revisi);
                    });
                }
            })
            ->groupBy('kode_kab')
            ->first();
        return $data;
    }

    public function rumus_1($komponens, $periode_filter, $id)
    {
        $data = [];
        foreach ($komponens as $komponen) {
            $row = [];
            $row = [
                'komponen' => $komponen['id'],
                'komponen_name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);
                if ($id === '1.1') {
                    if (sizeof($arr_periode) > 1) {
                        // $rev_kab =  $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1], 2, 1);
                        // $rev_kab_1 =  $this->get_rev(1, '00', $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                        $data_kab_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 2, 1); //, $rev_kab);
                        $data_kab_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [$arr_periode[1]], 2, 1); //, $rev_kab_1);
                        $data_prov_y = $this->get_data('00', $arr_periode[0], $arr_periode[1], 2, 1);
                        $data_prov_y_1 = $this->get_data('00', $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                        if ($arr_periode[1] != 1) {
                            $data_prov_q_1 = $this->get_data('00', $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                            // $rev_kab_q_1 = $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                            $data_kab_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1] - 1], 2, 1); //, $rev_kab_q_1);
                        } else {
                            $data_prov_q_1 = $this->get_data('00', $arr_periode[0] - 1, 4, 2, 1);
                            // $rev_kab_q_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, 4, 2, 1);
                            $data_kab_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [4], 2, 1); //, $rev_kab_q_1);
                        }
                        // $rev_prov_c = $this->get_rev(0, '00', $arr_periode[0], null, 2, 1);
                        // $rev_prov_c_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, null, 2, 1);
                        // $rev_kab_c = $this->get_rev(1, '00', $arr_periode[0], null, 2, 1);
                        // $rev_kab_c_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, null, 2, 1);

                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }

                        $data_kab_c = $this->get_data_cumulative(1, '00', $arr_periode[0], $q, 2, 1); //, $rev_kab_c);
                        $data_kab_c_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, $q, 2, 1); //, $rev_kab_c_1);
                        $data_prov_c = $this->get_data_cumulative(0, '00', $arr_periode[0], $q, 2, 1); //, $rev_prov_c);
                        $data_prov_c_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, $q, 2, 1); //, $rev_prov_c_1);

                        $row[$periode . 'yoy_kab'] = (
                            $data_kab_y
                            && $data_kab_y_1
                            && isset($data_kab_y_1->$komp_id)
                            && $data_kab_y_1->$komp_id != 0) ?
                            ($data_kab_y->$komp_id - $data_kab_y_1->$komp_id) / $data_kab_y_1->$komp_id * 100 : null;

                        $row[$periode . 'yoy_prov'] = (
                            $data_prov_y
                            && $data_prov_y_1
                            && isset($data_prov_y_1->$komp_id)
                            && $data_prov_y_1->$komp_id != 0) ?
                            ($data_prov_y->$komp_id - $data_prov_y_1->$komp_id) / $data_prov_y_1->$komp_id * 100 : null;

                        $row[$periode . 'qtq_kab'] = (
                            $data_kab_y &&
                            $data_kab_q_1 &&
                            isset($data_kab_q_1->$komp_id) &&
                            $data_kab_q_1->$komp_id != 0) ?
                            ($data_kab_y->$komp_id - $data_kab_q_1->$komp_id) / $data_kab_q_1->$komp_id * 100 : null;

                        $row[$periode . 'qtq_prov'] = (
                            $data_prov_y &&
                            $data_prov_q_1 &&
                            isset($data_prov_q_1->$komp_id) &&
                            $data_prov_q_1->$komp_id != 0) ?
                            ($data_prov_y->$komp_id - $data_prov_q_1->$komp_id) / $data_prov_q_1->$komp_id * 100 : null;

                        $row[$periode . 'ctc_kab'] = (
                            $data_kab_c &&
                            $data_kab_c_1 &&
                            isset($data_kab_c_1->$komp_id) &&
                            $data_kab_c_1->$komp_id != 0)
                            ? ($data_kab_c->$komp_id - $data_kab_c_1->$komp_id) / $data_kab_c_1->$komp_id * 100 : null;
                        $row[$periode . 'ctc_prov'] = (
                            $data_prov_c &&
                            $data_prov_c_1 &&
                            isset($data_prov_c_1->$komp_id) &&
                            $data_prov_c_1->$komp_id != 0)
                            ? ($data_prov_c->$komp_id - $data_prov_c_1->$komp_id) / $data_prov_c_1->$komp_id * 100 : null;
                    } else {
                        // $rev_kab_y = $this->get_rev(1, '00', $arr_periode[0], '', 2, 1);
                        // $rev_kab_y_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, '', 2, 1);
                        $data_kab_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_kab_y);
                        $data_kab_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_kab_y_1);

                        // $rev_prov_y = $this->get_rev(0, '00', $arr_periode[0], '', 2, 1);
                        // $rev_prov_y_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, '', 2, 1);
                        $data_prov_y = $this->get_data_cumulative(0, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_prov_y);
                        $data_prov_y_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_prov_y_1);

                        if (
                            $data_kab_y
                            && $data_kab_y_1
                            && isset($data_kab_y_1->$komp_id)
                            && $data_kab_y_1->$komp_id != 0
                        ) {
                            $row[$periode . 'yoy_kab'] =  ($data_kab_y->$komp_id - $data_kab_y_1->$komp_id) / $data_kab_y_1->$komp_id * 100;
                            $row[$periode . 'qtq_kab'] =  ($data_kab_y->$komp_id - $data_kab_y_1->$komp_id) / $data_kab_y_1->$komp_id * 100;
                            $row[$periode . 'ctc_kab'] =  ($data_kab_y->$komp_id - $data_kab_y_1->$komp_id) / $data_kab_y_1->$komp_id * 100;
                        } else {
                            $row[$periode . 'yoy_kab'] = null;
                            $row[$periode . 'qtq_kab'] = null;
                            $row[$periode . 'ctc_kab'] = null;
                        }

                        if (
                            $data_prov_y
                            && $data_prov_y_1
                            && isset($data_prov_y_1->$komp_id)
                            && $data_prov_y_1->$komp_id != 0
                        ) {
                            $row[$periode . 'yoy_prov'] =  ($data_prov_y->$komp_id - $data_prov_y_1->$komp_id) / $data_prov_y_1->$komp_id * 100;
                            $row[$periode . 'qtq_prov'] =  ($data_prov_y->$komp_id - $data_prov_y_1->$komp_id) / $data_prov_y_1->$komp_id * 100;
                            $row[$periode . 'ctc_prov'] =  ($data_prov_y->$komp_id - $data_prov_y_1->$komp_id) / $data_prov_y_1->$komp_id * 100;
                        } else {
                            $row[$periode . 'yoy_prov'] = null;
                            $row[$periode . 'qtq_prov'] = null;
                            $row[$periode . 'ctc_prov'] = null;
                        }
                    }
                } else if ($id === '1.2') {
                    if (sizeof($arr_periode) > 1) {
                        // $rev_kab_hb = $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1], 1, 1);
                        // $rev_kab_hk = $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1], 2, 1);
                        // $rev_kab_hb_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                        // $rev_kab_hk_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                        $data_kab_hb_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 1, 1); //, $rev_kab_hb);
                        $data_kab_hk_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 2, 1); //, $rev_kab_hk);
                        $data_kab_hb_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [$arr_periode[1]], 1, 1); //, $rev_kab_hb_1);
                        $data_kab_hk_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [$arr_periode[1]], 2, 1); //, $rev_kab_hk_1);

                        $data_prov_hb_y = $this->get_data('00', $arr_periode[0], $arr_periode[1], 1, 1);
                        $data_prov_hk_y = $this->get_data('00', $arr_periode[0], $arr_periode[1], 2, 1);
                        $data_prov_hb_y_1 = $this->get_data('00', $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                        $data_prov_hk_y_1 = $this->get_data('00', $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                        if ($arr_periode[1] != 1) {
                            // q2-q4
                            // $rev_kab_hb_q_1 = $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1] - 1, 1, 1);
                            // $rev_kab_hk_q_1 = $this->get_rev(1, '00', $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                            $data_kab_hb_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1] - 1], 1, 1); //, $rev_kab_hb_1);
                            $data_kab_hk_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1] - 1], 2, 1); //, $rev_kab_hk_1);

                            $data_prov_hb_q_1 = $this->get_data('00', $arr_periode[0], $arr_periode[1] - 1, 1, 1);
                            $data_prov_hk_q_1 = $this->get_data('00', $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                        } else {
                            // $rev_kab_hb_q_1 =  $this->get_rev(1, '00', $arr_periode[0] - 1, 4, 1, 1);
                            // $rev_kab_hk_q_1 =  $this->get_rev(1, '00', $arr_periode[0] - 1, 4, 2, 1);
                            $data_kab_hb_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [4], 1, 1); //, $rev_kab_hb_q_1);
                            $data_kab_hk_q_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [4], 2, 1); //, $rev_kab_hk_q_1);
                            $data_prov_hb_q_1 = $this->get_data('00', $arr_periode[0] - 1, 4, 1, 1);
                            $data_prov_hk_q_1 = $this->get_data('00', $arr_periode[0] - 1, 4, 2, 1);
                        }
                        // $rev_kab_hb_c = $this->get_rev(1, '00', $arr_periode[0], null, 1, 1);
                        // $rev_kab_hk_c = $this->get_rev(1, '00', $arr_periode[0], null, 2, 1);
                        // $rev_kab_hb_c_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, null, 1, 1);
                        // $rev_kab_hk_c_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, null, 2, 1);

                        // $rev_prov_hb_c = $this->get_rev(0, '00', $arr_periode[0], null, 1, 1);
                        // $rev_prov_hk_c = $this->get_rev(0, '00', $arr_periode[0], null, 2, 1);
                        // $rev_prov_hb_c_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, null, 1, 1);
                        // $rev_prov_hk_c_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, null, 2, 1);

                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                        $data_kab_hb_c = $this->get_data_cumulative(1, '00', $arr_periode[0], $q, 1, 1); //, $rev_kab_hb_c);
                        $data_kab_hk_c = $this->get_data_cumulative(1, '00', $arr_periode[0], $q, 2, 1); //, $rev_kab_hk_c);
                        $data_kab_hb_c_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, $q, 1, 1); //, $rev_kab_hb_c_1);
                        $data_kab_hk_c_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, $q, 2, 1); //, $rev_kab_hk_c_1);

                        $data_prov_hb_c = $this->get_data_cumulative(0, '00', $arr_periode[0], $q, 1, 1); //, $rev_prov_hb_c);
                        $data_prov_hk_c = $this->get_data_cumulative(0, '00', $arr_periode[0], $q, 2, 1); //, $rev_prov_hk_c);
                        $data_prov_hb_c_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, $q, 1, 1); //, $rev_prov_hb_c_1);
                        $data_prov_hk_c_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, $q, 2, 1); //, $rev_prov_hk_c_1);

                        $implisit_kab_y     = $data_kab_hb_y && $data_kab_hk_y && isset($data_kab_hk_y->$komp_id) && $data_kab_hk_y->$komp_id != 0 ? $data_kab_hb_y->$komp_id / $data_kab_hk_y->$komp_id * 100 : null;

                        $implisit_kab_y_1   = $data_kab_hb_y_1 && $data_kab_hk_y_1 && isset($data_kab_hk_y_1->$komp_id) && $data_kab_hk_y_1->$komp_id != 0 ? $data_kab_hb_y_1->$komp_id / $data_kab_hk_y_1->$komp_id * 100 : null;

                        $implisit_prov_y    = $data_prov_hb_y && $data_prov_hk_y && isset($data_prov_hk_y->$komp_id) && $data_prov_hk_y->$komp_id != 0 ? $data_prov_hb_y->$komp_id / $data_prov_hk_y->$komp_id * 100 : null;

                        $implisit_prov_y_1  = $data_prov_hb_y_1 && $data_prov_hk_y_1 && isset($data_prov_hk_y_1->$komp_id) && $data_prov_hk_y_1->$komp_id != 0 ? $data_prov_hb_y_1->$komp_id / $data_prov_hk_y_1->$komp_id * 100 : null;

                        $implisit_kab_q_1   = $data_kab_hb_q_1 && $data_kab_hk_q_1 && isset($data_kab_hk_q_1->$komp_id) && $data_kab_hk_q_1->$komp_id != 0 ? $data_kab_hb_q_1->$komp_id / $data_kab_hk_q_1->$komp_id * 100 : null;

                        $implisit_prov_q_1  = $data_prov_hb_q_1 && $data_prov_hk_q_1 && isset($data_prov_hk_q_1->$komp_id) && $data_prov_hk_q_1->$komp_id != 0 ? $data_prov_hb_q_1->$komp_id / $data_prov_hk_q_1->$komp_id * 100 : null;
                        $implisit_kab_c     = $data_kab_hb_c && $data_kab_hk_c && isset($data_kab_hk_c->$komp_id) && $data_kab_hk_c->$komp_id != 0 ? $data_kab_hb_c->$komp_id / $data_kab_hk_c->$komp_id * 100 : null;
                        $implisit_kab_c_1   = $data_kab_hb_c_1 && $data_kab_hk_c_1 && isset($data_kab_hk_c_1->$komp_id) && $data_kab_hk_c_1->$komp_id != 0 ? $data_kab_hb_c_1->$komp_id / $data_kab_hk_c_1->$komp_id * 100 : null;
                        $implisit_prov_c    = $data_prov_hb_c && $data_prov_hk_c && isset($data_prov_hk_c->$komp_id) && $data_prov_hk_c->$komp_id != 0 ? $data_prov_hb_c->$komp_id / $data_prov_hk_c->$komp_id * 100 : null;
                        $implisit_prov_c_1  = $data_prov_hb_c_1 && $data_prov_hk_c_1 && isset($data_prov_hk_c_1->$komp_id) && $data_prov_hk_c_1->$komp_id != 0 ? $data_prov_hb_c_1->$komp_id / $data_prov_hk_c_1->$komp_id * 100 : null;

                        $row[$periode . 'yoy_kab']  = ($implisit_kab_y && $implisit_kab_y_1 && $implisit_kab_y_1 != 0)      ? ($implisit_kab_y - $implisit_kab_y_1) / $implisit_kab_y_1 * 100 : null;
                        $row[$periode . 'yoy_prov'] = ($implisit_prov_y && $implisit_prov_y_1 && $implisit_prov_y_1 != 0)   ? ($implisit_prov_y - $implisit_prov_y_1) / $implisit_prov_y_1 * 100 : null;
                        $row[$periode . 'qtq_kab']  = ($implisit_kab_y && $implisit_kab_q_1 && $implisit_kab_q_1 != 0)      ? ($implisit_kab_y - $implisit_kab_q_1) / $implisit_kab_q_1 * 100 : null;
                        $row[$periode . 'qtq_prov'] = ($implisit_prov_y && $implisit_prov_q_1 && $implisit_prov_q_1 != 0)   ? ($implisit_prov_y - $implisit_prov_q_1)  / $implisit_prov_q_1 * 100 : null;
                        $row[$periode . 'ctc_kab']  = ($implisit_kab_c && $implisit_kab_c_1 && $implisit_kab_c_1 != 0)      ? ($implisit_kab_c - $implisit_kab_c_1) / $implisit_kab_c_1 * 100 : null;
                        $row[$periode . 'ctc_prov'] = ($implisit_prov_c && $implisit_prov_c_1 && $implisit_prov_c_1 != 0)   ? ($implisit_prov_c - $implisit_prov_c_1)  / $implisit_prov_c_1 * 100 : null;
                    } else {
                        // $rev_kab_hb_y = $this->get_rev(1, '00', $arr_periode[0], null, 1, 1);
                        // $rev_kab_hk_y = $this->get_rev(1, '00', $arr_periode[0], null, 2, 1);
                        // $rev_kab_hb_y_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, null, 1, 1);
                        // $rev_kab_hk_y_1 = $this->get_rev(1, '00', $arr_periode[0] - 1, null, 2, 1);

                        $data_kab_hb_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 1, 1); //, $rev_kab_hb_y);
                        $data_kab_hk_y = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_kab_hk_y);
                        $data_kab_hb_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1); //, $rev_kab_hb_y_1);
                        $data_kab_hk_y_1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_kab_hk_y_1);

                        // $rev_prov_hb_y = $this->get_rev(0, '00', $arr_periode[0], null, 1, 1);
                        // $rev_prov_hk_y = $this->get_rev(0, '00', $arr_periode[0], null, 2, 1);
                        // $rev_prov_hb_y_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, null, 1, 1);
                        // $rev_prov_hk_y_1 = $this->get_rev(0, '00', $arr_periode[0] - 1, null, 2, 1);

                        $data_prov_hb_y = $this->get_data_cumulative(0, '00', $arr_periode[0], [1, 2, 3, 4], 1, 1); //, $rev_prov_hb_y);
                        $data_prov_hk_y = $this->get_data_cumulative(0, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_prov_hk_y);
                        $data_prov_hb_y_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1); //, $rev_prov_hb_y_1);
                        $data_prov_hk_y_1 = $this->get_data_cumulative(0, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_prov_hk_y_1);

                        $implisit_kab_y     = $data_kab_hb_y && $data_kab_hk_y && isset($data_kab_hk_y->$komp_id) && $data_kab_hk_y->$komp_id != 0 ? $data_kab_hb_y->$komp_id / $data_kab_hk_y->$komp_id * 100 : null;
                        $implisit_kab_y_1   = $data_kab_hb_y_1 && $data_kab_hk_y_1 && isset($data_kab_hk_y_1->$komp_id) && $data_kab_hk_y_1->$komp_id != 0 ? $data_kab_hb_y_1->$komp_id / $data_kab_hk_y_1->$komp_id * 100 : null;
                        $implisit_prov_y    = $data_prov_hb_y && $data_prov_hk_y && isset($data_prov_hk_y->$komp_id) && $data_prov_hk_y->$komp_id != 0 ? $data_prov_hb_y->$komp_id / $data_prov_hk_y->$komp_id * 100 : null;
                        $implisit_prov_y_1  = $data_prov_hb_y_1 && $data_prov_hk_y_1 && isset($data_prov_hk_y_1->$komp_id) && $data_prov_hk_y_1->$komp_id != 0 ? $data_prov_hb_y_1->$komp_id / $data_prov_hk_y_1->$komp_id * 100 : null;

                        $row[$periode . 'yoy_kab'] = ($implisit_kab_y && $implisit_kab_y_1 && $implisit_kab_y_1 != 0) ? ($implisit_kab_y - $implisit_kab_y_1) / $implisit_kab_y_1 * 100 : null;
                        $row[$periode . 'yoy_prov'] = ($implisit_prov_y && $implisit_prov_y_1 && $implisit_prov_y_1 != 0) ? ($implisit_prov_y - $implisit_prov_y_1) / $implisit_prov_y_1 * 100 : null;
                        $row[$periode . 'qtq_kab'] = ($implisit_kab_y && $implisit_kab_y_1 && $implisit_kab_y_1 != 0) ? ($implisit_kab_y - $implisit_kab_y_1) / $implisit_kab_y_1 * 100 : null;
                        $row[$periode . 'qtq_prov'] = ($implisit_prov_y && $implisit_prov_y_1 && $implisit_prov_y_1 != 0) ? ($implisit_prov_y - $implisit_prov_y_1)  / $implisit_prov_y_1 * 100 : null;
                        $row[$periode . 'ctc_kab'] = ($implisit_kab_y && $implisit_kab_y_1 && $implisit_kab_y_1 != 0) ? ($implisit_kab_y - $implisit_kab_y_1) / $implisit_kab_y_1 * 100 : null;
                        $row[$periode . 'ctc_prov'] = ($implisit_prov_y && $implisit_prov_y_1 && $implisit_prov_y_1 != 0) ? ($implisit_prov_y - $implisit_prov_y_1)  / $implisit_prov_y_1 * 100 : null;
                    }
                }
            }
            $data[] = $row;
        }
        return $data;
    }

    public function rumus_2($list_wilayah, $periode_filter)
    {
        $data = [];
        foreach ($list_wilayah as $wil_id => $wilayah) {
            $row = [];
            $row = [
                'id' => $wil_id,
                'name' => $wilayah,
                'alias' => $wilayah
            ];
            $arr_periode = explode("Q", $periode_filter);
            if (sizeof($arr_periode) > 1) {
                $data_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                $data_y_1 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1], 2, 1);
                $data_y_2 = $this->get_data($wil_id, $arr_periode[0] - 2, $arr_periode[1], 2, 1);
                if ($arr_periode[1] != 1) {
                    $data_q_1 = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                    $data_q_2 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1] - 1, 2, 1);
                } else {
                    $data_q_1 = $this->get_data($wil_id, $arr_periode[0] - 1, 4, 2, 1);
                    $data_q_2 = $this->get_data($wil_id, $arr_periode[0] - 2, 4, 2, 1);
                }

                $q = [];
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }
                // $rev_c = $this->get_rev(0, $wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                // $rev_c_1 = $this->get_rev(0, $wil_id, $arr_periode[0] - 1, $arr_periode[1], 2, 1);
                // $rev_c_2 = $this->get_rev(0, $wil_id, $arr_periode[0] - 2, $arr_periode[1], 2, 1);

                $data_c = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], $q, 2, 1); //, $rev_c);
                $data_c_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, $q, 2, 1); //, $rev_c_1);
                $data_c_2 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 2, $q, 2, 1); //, $rev_c_2);

                $data_adhb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 1, 1);
                $data_adhb_y_1 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                $data_prov = $this->get_data('00', $arr_periode[0], $arr_periode[1], 2, 1);

                $data_total_kabkot = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 2, 1);
                // dd($data_total_kabkot);

                $row['yoy_current'] = ($data_y && $data_y_1 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0) ? round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb * 100, 2) : null;
                $row['yoy_prev']    = ($data_y_1 && $data_y_2 && isset($data_y_2->c_pdrb) && $data_y_2->c_pdrb != 0) ? round(($data_y_1->c_pdrb - $data_y_2->c_pdrb) / $data_y_2->c_pdrb * 100, 2) : null;
                $row['qtq_current'] = ($data_y && $data_q_1 && isset($data_q_1->c_pdrb) && $data_q_1->c_pdrb != 0) ? round(($data_y->c_pdrb - $data_q_1->c_pdrb) / $data_q_1->c_pdrb * 100, 2) : null;
                $row['qtq_prev']    = ($data_y_1 && $data_q_2 && isset($data_q_2->c_pdrb) && $data_q_2->c_pdrb != 0) ? round(($data_y_1->c_pdrb - $data_q_2->c_pdrb) / $data_q_2->c_pdrb * 100, 2) : null;
                $row['ctc_current'] = ($data_c && $data_c_1 && isset($data_c_1->c_pdrb) && $data_c_1->c_pdrb != 0) ? round(($data_c->c_pdrb - $data_c_1->c_pdrb) / $data_c_1->c_pdrb * 100, 2) : null;
                $row['ctc_prev']    = ($data_c_1 && $data_c_2 && isset($data_c_2->c_pdrb) && $data_c_2->c_pdrb != 0) ? round(($data_c_1->c_pdrb - $data_c_2->c_pdrb) / $data_c_2->c_pdrb * 100, 2) : null;
                $row['implisit_yoy'] = ($data_adhb_y && $data_y  && $data_adhb_y_1 && $data_y_1  && isset($data_y->c_pdrb) && $data_y->c_pdrb != 0  && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0  && isset($data_adhb_y_1->c_pdrb) && $data_adhb_y_1->c_pdrb != 0) ? round((($data_adhb_y->c_pdrb / $data_y->c_pdrb * 100) - ($data_adhb_y_1->c_pdrb / $data_y_1->c_pdrb * 100)) / ($data_adhb_y_1->c_pdrb / $data_y_1->c_pdrb * 100) * 100, 2) : null;
                $row['share_kabkot'] = ($data_y  && $data_total_kabkot && isset($data_total_kabkot->c_pdrb) && $data_total_kabkot->c_pdrb != 0) ? round($data_y->c_pdrb / $data_total_kabkot->c_pdrb * 100, 2) : null;
            } else {
                // $rev_y = $this->get_rev(0, $wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                // $rev_y_1 = $this->get_rev(0, $wil_id, $arr_periode[0] - 1, $arr_periode[1], 2, 1);
                // $rev_y_2 = $this->get_rev(0, $wil_id, $arr_periode[0] - 2, $arr_periode[1], 2, 1);
                // $rev_prov = $this->get_rev(0, '00', $arr_periode[0], $arr_periode[1], 2, 1);

                $data_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [$arr_periode[1]], 2, 1); //, $rev_y);
                $data_y_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [$arr_periode[1]], 2, 1); //, $rev_y_1);
                $data_y_2 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 2, [$arr_periode[1]], 2, 1); //, $rev_y_2);
                $data_adhb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [$arr_periode[1]], 1, 1); //, $rev_y);
                $data_adhb_y_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [$arr_periode[1]], 1, 1); //, $rev_y_1);

                // $data_prov_y = $this->get_data_cumulative(0, '00', $arr_periode[0], [$arr_periode[1]], 2, 1); //, $rev_prov);
                $data_total_kabkot = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_prov);

                $row['yoy_current'] = $data_y && $data_y_1 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0 ? round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb * 100, 2) : null;
                $row['yoy_prev'] = $data_y_1 && $data_y_2 && isset($data_y_2->c_pdrb) && $data_y_2->c_pdrb != 0 ? round(($data_y_1->c_pdrb - $data_y_2->c_pdrb) / $data_y_2->c_pdrb * 100, 2) : null;
                $row['qtq_current'] = $data_y && $data_y_1 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0 ? round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb * 100, 2) : null;
                $row['qtq_prev'] = $data_y_1 && $data_y_2 && isset($data_y_2->c_pdrb) && $data_y_2->c_pdrb != 0 ? round(($data_y_1->c_pdrb - $data_y_2->c_pdrb) / $data_y_2->c_pdrb * 100, 2) : null;
                $row['ctc_current'] = $data_y && $data_y_1 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0 ? round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb * 100, 2) : null;
                $row['ctc_prev'] = $data_y_1 && $data_y_2 && isset($data_y_2->c_pdrb) && $data_y_2->c_pdrb != 0 ? round(($data_y_1->c_pdrb - $data_y_2->c_pdrb) / $data_y_2->c_pdrb * 100, 2) : null;
                $row['implisit_yoy'] = $data_y  && $data_adhb_y && $data_y_1 && $data_adhb_y_1 && isset($data_y->c_pdrb) && $data_y->c_pdrb != 0 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0
                    && isset($data_adhb_y_1->c_pdrb) && $data_adhb_y_1->c_pdrb != 0
                    ? round((($data_y->c_pdrb / $data_adhb_y->c_pdrb * 100) - ($data_y_1->c_pdrb / $data_adhb_y_1->c_pdrb * 100)) / ($data_y_1->c_pdrb / $data_adhb_y_1->c_pdrb * 100) * 100, 2) : null;
                $row['share_kabkot'] = $data_y  && $data_total_kabkot && isset($data_total_kabkot->c_pdrb) && $data_total_kabkot->c_pdrb != 0 ? round($data_y->c_pdrb / $data_total_kabkot->c_pdrb * 100, 2) : null;
            }
            $data[] = $row;
        }

        $dk_y0 = $this->get_data_cumulative(1, "00", $arr_periode[0], [$arr_periode[1]], 2, 1);
        $dk_y1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [$arr_periode[1]], 2, 1);
        $dk_y2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, [$arr_periode[1]], 2, 1);

        if ($arr_periode[1] != 1) {
            $dk_q1 = $this->get_data_cumulative(1, "00", $arr_periode[0], [$arr_periode[1] - 1], 2, 1);
            $dk_q2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [$arr_periode[1] - 1], 2, 1);
        } else {
            $dk_q1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [4], 2, 1);
            $dk_q2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, [4], 2, 1);
        }

        $q = [];
        for ($i = 1; $i <= $arr_periode[1]; $i++) {
            $q[] = $i;
        }

        $dk_c0 = $this->get_data_cumulative(0, "00", $arr_periode[0], $q, 2, 1); //, $rev_c);
        $dk_c1 = $this->get_data_cumulative(0, "00", $arr_periode[0] - 1, $q, 2, 1); //, $rev_c_1);
        $dk_c2 = $this->get_data_cumulative(0, "00", $arr_periode[0] - 2, $q, 2, 1); //, $rev_c_2);

        $dk_hb_y0 = $this->get_data_cumulative(0, "00", $arr_periode[0], [$arr_periode[1]], 1, 1);
        $dk_hb_y1 = $this->get_data_cumulative(0, "00", $arr_periode[0] - 1, [$arr_periode[1]], 1, 1);

        $data['total_kabkot'] = [
            "id" => "99",
            "name" => "Total kabupaten Kota",
            "alias" => "17 Kabkot",
            "yoy_current"   => ($dk_y0 && $dk_y1 && isset($dk_y1->c_pdrb) && $dk_y1->c_pdrb != 0) ? round(($dk_y0->c_pdrb - $dk_y1->c_pdrb) / $dk_y1->c_pdrb * 100, 2) : null,
            "yoy_prev"      => ($dk_y1 && $dk_y2 && isset($dk_y2->c_pdrb) && $dk_y2->c_pdrb != 0) ? round(($dk_y1->c_pdrb - $dk_y2->c_pdrb) / $dk_y2->c_pdrb * 100, 2) : null,
            "qtq_current"   => ($dk_y0 && $dk_q1 && isset($dk_q1->c_pdrb) && $dk_q1->c_pdrb != 0) ? round(($dk_y0->c_pdrb - $dk_q1->c_pdrb) / $dk_q1->c_pdrb * 100, 2) : null,
            "qtq_prev"      => ($dk_y1 && $dk_q2 && isset($dk_q2->c_pdrb) && $dk_q2->c_pdrb != 0) ? round(($dk_y1->c_pdrb - $dk_q2->c_pdrb) / $dk_q2->c_pdrb * 100, 2) : null,
            "ctc_current"   => ($dk_c0 && $dk_c1 && isset($dk_c1->c_pdrb) && $dk_c1->c_pdrb != 0) ? round(($dk_c0->c_pdrb - $dk_c1->c_pdrb) / $dk_c1->c_pdrb * 100, 2) : null,
            "ctc_prev"      => ($dk_c1 && $dk_c2 && isset($dk_c2->c_pdrb) && $dk_c2->c_pdrb != 0) ? round(($dk_c1->c_pdrb - $dk_c2->c_pdrb) / $dk_c2->c_pdrb * 100) : null,
            "implisit_yoy"  => ($dk_hb_y0 && $dk_y0  && $dk_hb_y1 && $dk_y1
                && isset($dk_y0->c_pdrb) && $dk_y0->c_pdrb != 0
                && isset($dk_y1->c_pdrb) && $dk_y1->c_pdrb != 0
                && isset($dk_hb_y1->c_pdrb) && $dk_hb_y1->c_pdrb != 0)
                ? round((($dk_hb_y0->c_pdrb / $dk_y0->c_pdrb * 100) - ($dk_hb_y1->c_pdrb / $dk_y1->c_pdrb * 100)) / ($dk_hb_y1->c_pdrb / $dk_y1->c_pdrb * 100) * 100, 2) : null,
            "share_kabkot"  => ($dk_y0  && $dk_y0 && isset($dk_y0->c_pdrb) && $dk_y0->c_pdrb != 0) ? round($dk_y0->c_pdrb / $dk_y0->c_pdrb * 100, 2) : null,
        ];

        // dd($data);
        return $data;
    }

    public function rumus_3($list_wilayah, $komponens, $periode_filter, $id)
    {
        $data = [];
        $arr_periode = explode("Q", $periode_filter);
        // dk = data total 17 kabkot
        if (sizeof($arr_periode) > 1) {
            $dk_y0 = $this->get_data_cumulative(1, "00", $arr_periode[0], [$arr_periode[1]], 2, 1);
            $dk_y1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [$arr_periode[1]], 2, 1);
            $dk_y2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, [$arr_periode[1]], 2, 1);

            if ($arr_periode[1] != 1) {
                $dk_q1 = $this->get_data_cumulative(1, "00", $arr_periode[0], [$arr_periode[1] - 1], 2, 1);
                $dk_q2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [$arr_periode[1] - 1], 2, 1);
            } else {
                $dk_q1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [4], 2, 1);
                $dk_q2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, [4], 2, 1);
            }
            $q = [];
            for ($i = 1; $i <= $arr_periode[1]; $i++) {
                $q[] = $i;
            }
            $dk_c0    = $this->get_data_cumulative(1, "00", $arr_periode[0], $q, 2, 1); //, $rev_c);
            $dk_c1    = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, $q, 2, 1); //, $rev_c_1);
            $dk_c2    = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, $q, 2, 1); //, $rev_c_2);
            $dk_hb_y0 = $this->get_data_cumulative(1, "00", $arr_periode[0], [$arr_periode[1]], 1, 1);
            $dk_hb_y1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [$arr_periode[1]], 1, 1);
        } else {
            $dk_y0 = $this->get_data_cumulative(1, "00", $arr_periode[0], [1, 2, 3, 4], 2, 1);
            $dk_y1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1);
            $dk_y2 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 2, [1, 2, 3, 4], 2, 1);
            $dk_q1 = $dk_y1;
            $dk_q2 = $dk_y2;
            $dk_c0    = $dk_y0;
            $dk_c1    = $dk_y1;
            $dk_c2    = $dk_y2;
            $dk_hb_y0 = $this->get_data_cumulative(1, "00", $arr_periode[0], [1, 2, 3, 4], 1, 1);
            $dk_hb_y1 = $this->get_data_cumulative(1, "00", $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1);
        }

        if ($id == "1.4") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                    $pdrb_y_1 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1], 2, 1);
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_y);
                    $pdrb_y_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_y_1);
                }
                if ($pdrb_y && $pdrb_y_1) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_y0 && $dk_y1) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] =  isset($dk_y0->$komp_id) && $dk_y1->$komp_id != 0 ? ($dk_y0->$komp_id - $dk_y1->$komp_id) / $dk_y1->$komp_id * 100 : null;
                }
            }
        } else if ($id == "1.5") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                    if ($arr_periode[1] == 1) {
                        $pdrb_q_1 = $this->get_data($wil_id, $arr_periode[0] - 1, 4, 2, 1);
                    } else {
                        $pdrb_q_1 = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                    }
                    if ($pdrb_y && $pdrb_q_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id * 100 : null;
                        }
                    }
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_y);
                    $pdrb_y_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_y_1);
                    if ($pdrb_y && $pdrb_y_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                        }
                    }
                }
                $data[] = $row;
            }

            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_y0 && $dk_q1) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] =  isset($dk_y0->$komp_id) && $dk_q1->$komp_id != 0 ? ($dk_y0->$komp_id - $dk_q1->$komp_id) / $dk_q1->$komp_id * 100 : null;
                }
            }
        } else if ($id == "1.6") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                $q = [];
                if (sizeof($arr_periode) > 1) {
                    for ($i = 1; $i <= $arr_periode[1]; $i++) {
                        $q[] = $i;
                    }
                } else {
                    $q = [1, 2, 3, 4];
                }
                $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], $q, 2, 1);
                $pdrb_y_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, $q, 2, 1);
                if ($pdrb_y && $pdrb_y_1) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_c0 && $dk_c1) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] =  isset($dk_c0->$komp_id) && $dk_c1->$komp_id != 0 ? ($dk_c0->$komp_id - $dk_c1->$komp_id) / $dk_c1->$komp_id * 100 : null;
                }
            }
        } else if ($id == "1.7") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 1, 1);
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 1, 1);
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id / $pdrb_y->c_pdrb * 100) : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_hb_y0) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_hb_y0->$komp_id) && isset($dk_hb_y0->c_pdrb) && $dk_hb_y0->c_pdrb != 0 ? ($dk_hb_y0->$komp_id / $dk_hb_y0->c_pdrb * 100) : null;
                }
            }
        } else if ($id == "1.8") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1);
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id / $pdrb_y->c_pdrb * 100) : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_y0) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_y0->$komp_id) && isset($dk_y0->c_pdrb) && $dk_y0->c_pdrb != 0 ? ($dk_y0->$komp_id / $dk_y0->c_pdrb * 100) : null;
                }
            }
        } else if ($id == "1.9") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_hb = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 1, 1);
                    $pdrb_hk = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                    $pdrb_hb_1 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                    $pdrb_hk_1 = $this->get_data($wil_id, $arr_periode[0] - 1, $arr_periode[1], 2, 1);
                } else {
                    $pdrb_hb = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 1, 1);
                    $pdrb_hk = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1);
                    $pdrb_hb_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1);
                    $pdrb_hk_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1,  [1, 2, 3, 4], 2, 1);
                }
                if ($pdrb_hb && $pdrb_hk && $pdrb_hb_1 && $pdrb_hk_1) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_hk->$komp_id)
                            && $pdrb_hk->$komp_id != 0
                            && isset($pdrb_hb_1->$komp_id)
                            && $pdrb_hb_1->$komp_id != 0
                            && isset($pdrb_hk_1->$komp_id)
                            && $pdrb_hk_1->$komp_id != 0 ?
                            (($pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100) - ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100)) / ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100) * 100 : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_hb_y0 && $dk_y0 && $dk_hb_y1 && $dk_y1) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_y0->$komp_id)
                        && $dk_y0->$komp_id != 0
                        && isset($dk_hb_y1->$komp_id)
                        && $dk_hb_y1->$komp_id != 0
                        && isset($dk_y1->$komp_id)
                        && $dk_y1->$komp_id != 0 ?
                        (($pdrb_hb->$komp_id / $dk_y0->$komp_id * 100) - ($dk_hb_y1->$komp_id / $dk_y1->$komp_id * 100)) / ($dk_hb_y1->$komp_id / $dk_y1->$komp_id * 100) * 100 : null;
                }
            }
        } else if ($id == "1.10") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_hb = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 1, 1);
                    $pdrb_hk = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                    if ($arr_periode[1] == 1) {
                        $pdrb_hb_1 = $this->get_data($wil_id, $arr_periode[0] - 1, 4, 1, 1);
                        $pdrb_hk_1 = $this->get_data($wil_id, $arr_periode[0] - 1, 4, 2, 1);
                    } else {
                        $pdrb_hb_1 = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1] - 1, 1, 1);
                        $pdrb_hk_1 = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                    }
                } else {
                    $pdrb_hb = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 1, 1);
                    $pdrb_hk = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1);
                    $pdrb_hb_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1);
                    $pdrb_hk_1 = $this->get_data_cumulative(0, $wil_id, $arr_periode[0] - 1,  [1, 2, 3, 4], 2, 1);
                }
                if ($pdrb_hb && $pdrb_hk && $pdrb_hb_1 && $pdrb_hk_1) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_hk->$komp_id)
                            && $pdrb_hk->$komp_id != 0
                            && isset($pdrb_hb_1->$komp_id)
                            && $pdrb_hb_1->$komp_id != 0
                            && isset($pdrb_hk_1->$komp_id)
                            && $pdrb_hk_1->$komp_id != 0 ?
                            (($pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100) - ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100)) / ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100) * 100 : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if (sizeof($arr_periode) > 1) {
                if ($arr_periode[1] == 1) {
                    $dk_hb_q1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [4], 1, 1);
                } else {
                    $dk_hb_q1 = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1] - 1], 1, 1);
                }
            } else {

                $dk_hb_q1 = $this->get_data_cumulative(1, '00', $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1);
            }
            if ($dk_hb_y0 && $dk_y0 && $dk_hb_q1 && $dk_q1) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_y0->$komp_id)
                        && $dk_y0->$komp_id != 0
                        && isset($dk_hb_q1->$komp_id)
                        && $dk_hb_q1->$komp_id != 0
                        && isset($dk_y1->$komp_id)
                        && $dk_y1->$komp_id != 0 ?
                        (($pdrb_hb->$komp_id / $dk_y0->$komp_id * 100) - ($dk_hb_q1->$komp_id / $dk_y1->$komp_id * 100)) / ($dk_hb_q1->$komp_id / $dk_y1->$komp_id * 100) * 100 : null;
                }
            }
        } else if ($id == "1.15") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 1, 1);
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 1, 1);
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_hb_y0) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_hb_y0->$komp_id) ? $dk_hb_y0->$komp_id : null;
                }
            }
        } else if ($id == "1.16") {
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row = [];
                $row = [
                    'id' => $wil_id,
                    'name' => $wilayah,
                    'alias' => $wilayah
                ];
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = $this->get_data($wil_id, $arr_periode[0], $arr_periode[1], 2, 1);
                } else {
                    $pdrb_y = $this->get_data_cumulative(0, $wil_id, $arr_periode[0], [1, 2, 3, 4], 2, 1);
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                }
                $data[] = $row;
            }
            $data['total_kabkot'] = [
                'id' => '99',
                'name' => 'Total 17 Kabkot',
                'alias' => '17 kabkot'
            ];
            if ($dk_y0) {
                foreach ($komponens as $komp) {
                    $komp_id = $komp['id'];
                    $data['total_kabkot'][$komp_id] = isset($dk_y0->$komp_id) ? $dk_y0->$komp_id : null;
                }
            }
        }
        return $data;
    }

    public function rumus_4($komponens, $periode_filter, $id)
    {
        $data = [];
        foreach ($komponens as $komponen) {
            $row = [];
            $row = [
                'komponen' => $komponen['id'],
                'komponen_name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);
                if ($id == '1.11') {
                    if (sizeof($arr_periode) > 1) {
                        // jika ada Q, misal 2024Q1'
                        $data_hb_prov = $this->get_data('00', $arr_periode[0], $arr_periode[1], 1, 1);
                        $data_hk_prov = $this->get_data('00', $arr_periode[0], $arr_periode[1], 2, 1);
                        $data_hb_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 1, 1);
                        $data_hk_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], [$arr_periode[1]], 2, 1);
                    } else {
                        $data_hb_prov = $this->get_data_cumulative(0, '00', $arr_periode[0], [1, 2, 3, 4], 1, 1);
                        $data_hk_prov = $this->get_data_cumulative(0, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1);
                        $data_hb_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 1, 1);
                        $data_hk_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], [1, 2, 3, 4], 2, 1);
                    }
                } else if ($id == '1.12') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $data_hb_prov = $this->get_data_cumulative(0, '00', $arr_periode[0], $q, 1, 1);
                    $data_hk_prov = $this->get_data_cumulative(0, '00', $arr_periode[0], $q, 2, 1);
                    $data_hb_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], $q, 1, 1);
                    $data_hk_kab = $this->get_data_cumulative(1, '00', $arr_periode[0], $q, 2, 1);
                }
                $row[$periode . 'adhb'] = $data_hb_kab && isset($data_hb_kab->$komp_id) && $data_hb_prov && isset($data_hb_prov->$komp_id) && $data_hb_prov->$komp_id != 0 ?  $data_hb_kab->$komp_id / $data_hb_prov->$komp_id  : null;
                $row[$periode . 'adhk'] = $data_hk_kab && isset($data_hk_kab->$komp_id) && $data_hk_prov && isset($data_hk_prov->$komp_id) && $data_hk_prov->$komp_id != 0 ?  $data_hk_kab->$komp_id / $data_hk_prov->$komp_id : null;
            }
            $data[] = $row;
        }
        // dd($data);
        return $data;
    }

    public function rumus_5($komponens, $wilayah_filter, $periode_filter)
    {
        $data = [];
        foreach ($komponens as $komp) {
            $row = [
                'id' => $komp['id'],
                'name' => $komp['name'],
                'alias' => $komp['alias']
            ];
            $komp_id = $komp['id'];
            $arr_periode = explode("Q", $periode_filter);

            if (sizeof($arr_periode) > 1) {
                $data_hb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1);
                $data_hk_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1);
                $data_hb_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                $data_hk_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                if ($arr_periode[1] != 1) {
                    // q2-q4
                    $data_hb_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 1, 1);
                    $data_hk_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1);
                } else {
                    $data_hb_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 1, 1);
                    $data_hk_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1);
                }

                $q = [];
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $data_hb_c = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], $q, 1, 1); //, $rev_hb_y);
                $data_hk_c = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], $q, 2, 1); //, $rev_hk_y);
                $data_hb_c_1 = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 1, 1); //, $rev_hb_y_1);
                $data_hk_c_1 = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 2, 1); //, $rev_hk_y_1);

                $row['yoy'] = $data_hk_y && $data_hk_y_1 && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0 ? ($data_hk_y->$komp_id - $data_hk_y_1->$komp_id) / $data_hk_y_1->$komp_id * 100 : null;
                $row['qtq'] = $data_hk_y && $data_hk_q_1 && isset($data_hk_q_1->$komp_id) && $data_hk_q_1->$komp_id != 0 ? ($data_hk_y->$komp_id - $data_hk_q_1->$komp_id) / $data_hk_q_1->$komp_id * 100 : null;
                $row['ctc'] = $data_hk_c && $data_hk_c_1 && isset($data_hk_c_1->$komp_id) && $data_hk_c_1->$komp_id != 0 ? ($data_hk_c->$komp_id - $data_hk_c_1->$komp_id) / $data_hk_c_1->$komp_id * 100 : null;
                $row['implisit_yoy'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) * 100 : null;
                $row['implisit_qtq'] = $data_hb_y && $data_hk_y && $data_hb_q_1 && $data_hk_q_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_q_1->$komp_id) && $data_hb_q_1->$komp_id != 0   && isset($data_hk_q_1->$komp_id) && $data_hk_q_1->$komp_id != 0   ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100)) / ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100) * 100 : null;
                $row['implisit_ctc'] = $data_hb_c && $data_hk_c && $data_hb_c_1 && $data_hk_c_1  && isset($data_hk_c->$komp_id) && $data_hk_c->$komp_id != 0   && isset($data_hb_c_1->$komp_id) && $data_hb_c_1->$komp_id != 0   && isset($data_hk_c_1->$komp_id) && $data_hk_c_1->$komp_id != 0   ? (($data_hb_c->$komp_id / $data_hk_c->$komp_id * 100)  - ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100)) / ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100) * 100 : null;
            } else {

                $data_hb_y = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, 1); //, $rev_hb_y);
                $data_hk_y = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_hk_y);
                $data_hb_y_1 = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1); //, $rev_hb_y_1);
                $data_hk_y_1 = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_hk_y_1);

                $row['yoy'] = $data_hk_y && $data_hk_y_1 && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0 ? ($data_hk_y->$komp_id - $data_hk_y_1->$komp_id) / $data_hk_y_1->$komp_id * 100 : null;
                $row['qtq'] = $data_hk_y && $data_hk_y_1 && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0 ? ($data_hk_y->$komp_id - $data_hk_y_1->$komp_id) / $data_hk_y_1->$komp_id * 100 : null;
                $row['ctc'] = $data_hk_y && $data_hk_y_1 && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0 ? ($data_hk_y->$komp_id - $data_hk_y_1->$komp_id) / $data_hk_y_1->$komp_id * 100 : null;
                $row['implisit_yoy'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) * 100 : null;
                $row['implisit_qtq'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) * 100 : null;
                $row['implisit_ctc'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) * 100 : null;
            }
            $data[] = $row;
        }
        return $data;
    }

    public function rumus_6($komponens, $wilayah_filter, $periode_filter)
    {
        $data = [];
        foreach ($komponens as $komp) {
            $row = [
                'id' => $komp['id'],
                'name' => $komp['name'],
                'alias' => $komp['alias']
            ];
            $komp_id = $komp['id'];
            $arr_periode = explode("Q", $periode_filter);
            if (sizeof($arr_periode) > 1) {
                $data_hb_y_rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1);
                $data_hk_y_rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1);

                $q_hb_y_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 1, [2, 3]);
                $q_hk_y_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, [2, 3]);
                $data_hb_y_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, [2, 3], $q_hb_y_revisi);
                $data_hk_y_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, [2, 3], $q_hk_y_revisi);

                $data_hb_y_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 1, 1);
                $data_hk_y_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1);

                $q_hb_y1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 1, [2, 3]);
                $q_hk_y1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, [2, 3]);
                $data_hb_y_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 1, [2, 3], $q_hb_y1_revisi);
                $data_hk_y_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 2, [2, 3], $q_hk_y1_revisi);

                if ($arr_periode[1] == 1) {
                    $data_hb_q_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 1, 1);
                    $data_hk_q_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1);

                    $q_hb_q1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 1, [2, 3]);
                    $q_hk_q1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, [2, 3]);

                    $data_hb_q_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 1, [2, 3], $q_hb_q1_revisi);
                    $data_hk_q_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 2, [2, 3], $q_hk_q1_revisi);
                } else {

                    $data_hb_q_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 1, 1);
                    $data_hk_q_1_rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1);

                    $q_hb_q1_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 1, [2, 3]);
                    $q_hk_q1_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, [2, 3]);

                    $data_hb_q_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 1, [2, 3], $q_hb_q1_revisi);
                    $data_hk_q_1_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 2, [2, 3], $q_hk_q1_revisi);
                }
                $q = [];
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $data_hb_c_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], $q, 1, 1); //, $rev_hb_y_rilis);
                $data_hk_c_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], $q, 2, 1); //, $rev_hk_y_rilis);
                $q_hb_c_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 1, [2, 3]);
                $q_hk_c_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, [2, 3]);
                $data_hb_c_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 1, [2, 3], $q_hb_c_revisi); //, $rev_hb_y_revisi);
                $data_hk_c_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 2, [2, 3], $q_hk_c_revisi); //, $rev_hk_y_revisi);

                $data_hb_c_1_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 1, 1); //, $rev_hb_y_1_rilis);
                $data_hk_c_1_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 2, 1); //, $rev_hk_y_1_rilis);
                $q_hb_c1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 1, [2, 3]);
                $q_hk_c1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, [2, 3]);
                $data_hb_c_1_revisi = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 1, $q_hb_c1_revisi); //, $rev_hb_y_1_revisi);
                $data_hk_c_1_revisi = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, $q, 2, $q_hk_c1_revisi); //, $rev_hk_y_1_revisi);



            } else {
                $data_hb_c_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, 1); //, $rev_hb_y_rilis);
                $data_hk_c_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1); //, $rev_hk_y_rilis);
                $q_hb_c_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 1, [2, 3]);
                $q_hk_c_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, [2, 3]);
                $data_hb_c_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, [2, 3], $q_hb_c_revisi); //, $rev_hb_y_revisi);
                $data_hk_c_revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, [2, 3], $q_hk_c_revisi); //, $rev_hk_y_revisi);

                $data_hb_c_1_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1); //, $rev_hb_y_1_rilis);
                $data_hk_c_1_rilis = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1); //, $rev_hk_y_1_rilis);
                $q_hb_c1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 1, [2, 3]);
                $q_hk_c1_revisi = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, [2, 3]);
                $data_hb_c_1_revisi = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 1, $q_hb_c1_revisi); //, $rev_hb_y_1_revisi);
                $data_hk_c_1_revisi = $this->get_data_cumulative(0, $wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, $q_hk_c1_revisi); //, $rev_hk_y_1_revisi);

                $data_hb_y_rilis    = $data_hb_c_rilis;
                $data_hk_y_rilis    = $data_hk_c_rilis;
                $data_hb_y_revisi   = $data_hb_c_revisi;
                $data_hk_y_revisi   = $data_hk_c_revisi;

                $data_hb_y_1_rilis = $data_hb_c_1_rilis;
                $data_hk_y_1_rilis = $data_hk_c_1_rilis;
                $data_hb_y_1_revisi = $data_hb_c_1_revisi;
                $data_hk_y_1_revisi = $data_hk_c_1_revisi;

                $data_hb_q_1_rilis = $data_hb_c_1_rilis;
                $data_hk_q_1_rilis = $data_hk_c_1_rilis;
                $data_hb_q_1_revisi = $data_hb_c_1_revisi;
                $data_hk_q_1_revisi = $data_hk_c_1_revisi;
            }
            $row['yoy_rilis']   = $data_hk_y_rilis  && $data_hk_y_1_rilis   && isset($data_hk_y_1_rilis->$komp_id)  && $data_hk_y_1_rilis->$komp_id != 0    ? ($data_hk_y_rilis->$komp_id -     $data_hk_y_1_rilis->$komp_id)   / $data_hk_y_1_rilis->$komp_id  * 100 : null;
            $row['yoy_revisi']  = $data_hk_y_revisi && $data_hk_y_1_revisi  && isset($data_hk_y_1_revisi->$komp_id) && $data_hk_y_1_revisi->$komp_id != 0   ? ($data_hk_y_revisi->$komp_id -    $data_hk_y_1_revisi->$komp_id)  / $data_hk_y_1_revisi->$komp_id * 100 : null;
            $row['qtq_rilis']   = $data_hk_y_rilis  && $data_hk_q_1_rilis   && isset($data_hk_q_1_rilis->$komp_id)  && $data_hk_q_1_rilis->$komp_id != 0    ? ($data_hk_y_rilis->$komp_id -     $data_hk_q_1_rilis->$komp_id)   / $data_hk_q_1_rilis->$komp_id  * 100 : null;
            $row['qtq_revisi']  = $data_hk_y_revisi && $data_hk_q_1_revisi  && isset($data_hk_q_1_revisi->$komp_id) && $data_hk_q_1_revisi->$komp_id != 0   ? ($data_hk_y_revisi->$komp_id -    $data_hk_q_1_revisi->$komp_id)  / $data_hk_q_1_revisi->$komp_id * 100 : null;
            $row['ctc_rilis']   = $data_hk_c_rilis  && $data_hk_c_1_rilis   && isset($data_hk_c_1_rilis->$komp_id)  && $data_hk_c_1_rilis->$komp_id != 0    ? ($data_hk_c_rilis->$komp_id -     $data_hk_c_1_rilis->$komp_id)   / $data_hk_c_1_rilis->$komp_id  * 100 : null;
            $row['ctc_revisi']  = $data_hk_c_revisi && $data_hk_c_1_revisi  && isset($data_hk_c_1_revisi->$komp_id) && $data_hk_c_1_revisi->$komp_id != 0   ? ($data_hk_c_revisi->$komp_id -    $data_hk_c_1_revisi->$komp_id)  / $data_hk_c_1_revisi->$komp_id * 100 : null;

            $row['implisit_yoy_rilis'] = $data_hb_y_rilis
                && $data_hk_y_rilis
                && $data_hb_y_1_rilis
                && $data_hk_y_1_rilis
                && isset($data_hk_y_rilis->$komp_id)
                && $data_hk_y_rilis->$komp_id != 0
                && isset($data_hb_y_1_rilis->$komp_id)
                && $data_hb_y_1_rilis->$komp_id != 0
                && isset($data_hk_y_1_rilis->$komp_id)
                && $data_hk_y_1_rilis->$komp_id != 0 ?
                (($data_hb_y_rilis->$komp_id / $data_hk_y_rilis->$komp_id * 100) - ($data_hb_y_1_rilis->$komp_id / $data_hk_y_1_rilis->$komp_id * 100)) / ($data_hb_y_1_rilis->$komp_id / $data_hk_y_1_rilis->$komp_id * 100) * 100
                : null;

            $row['implisit_yoy_revisi'] = $data_hb_y_revisi
                && $data_hk_y_revisi
                && $data_hb_y_1_revisi
                && $data_hk_y_1_revisi
                && isset($data_hk_y_revisi->$komp_id)
                && $data_hk_y_revisi->$komp_id != 0
                && isset($data_hb_y_1_revisi->$komp_id)
                && $data_hb_y_1_revisi->$komp_id != 0
                && isset($data_hk_y_1_revisi->$komp_id)
                && $data_hk_y_1_revisi->$komp_id != 0 ?
                (($data_hb_y_revisi->$komp_id / $data_hk_y_revisi->$komp_id * 100) - ($data_hb_y_1_revisi->$komp_id / $data_hk_y_1_revisi->$komp_id * 100)) / ($data_hb_y_1_revisi->$komp_id / $data_hk_y_1_revisi->$komp_id * 100) * 100
                : null;

            $row['implisit_qtq_rilis'] = $data_hb_y_rilis
                && $data_hk_y_rilis
                && $data_hb_q_1_rilis
                && $data_hk_q_1_rilis
                && isset($data_hk_y_rilis->$komp_id)
                && $data_hk_y_rilis->$komp_id != 0
                && isset($data_hb_q_1_rilis->$komp_id)
                && $data_hb_q_1_rilis->$komp_id != 0
                && isset($data_hk_q_1_rilis->$komp_id)
                && $data_hk_q_1_rilis->$komp_id != 0 ?
                (($data_hb_y_rilis->$komp_id / $data_hk_y_rilis->$komp_id * 100) - ($data_hb_q_1_rilis->$komp_id / $data_hk_q_1_rilis->$komp_id * 100)) / ($data_hb_q_1_rilis->$komp_id / $data_hk_q_1_rilis->$komp_id * 100) * 100
                : null;

            $row['implisit_qtq_revisi'] = $data_hb_y_revisi
                && $data_hk_y_revisi
                && $data_hb_q_1_revisi
                && $data_hk_q_1_revisi
                && isset($data_hk_y_revisi->$komp_id)
                && $data_hk_y_revisi->$komp_id != 0
                && isset($data_hb_q_1_revisi->$komp_id)
                && $data_hb_q_1_revisi->$komp_id != 0
                && isset($data_hk_q_1_revisi->$komp_id)
                && $data_hk_q_1_revisi->$komp_id != 0 ?
                (($data_hb_y_revisi->$komp_id / $data_hk_y_revisi->$komp_id * 100) - ($data_hb_q_1_revisi->$komp_id / $data_hk_q_1_revisi->$komp_id * 100)) / ($data_hb_q_1_revisi->$komp_id / $data_hk_q_1_revisi->$komp_id * 100) * 100
                : null;

            $row['implisit_ctc_rilis'] = $data_hb_c_rilis
                && $data_hk_c_rilis
                && $data_hb_c_1_rilis
                && $data_hk_c_1_rilis
                && isset($data_hk_c_rilis->$komp_id)
                && $data_hk_c_rilis->$komp_id != 0
                && isset($data_hb_c_1_rilis->$komp_id)
                && $data_hb_c_1_rilis->$komp_id != 0
                && isset($data_hk_c_1_rilis->$komp_id)
                && $data_hk_c_1_rilis->$komp_id != 0 ?
                (($data_hb_c_rilis->$komp_id / $data_hk_c_rilis->$komp_id * 100) - ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)) / ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100) * 100
                : null;
            $row['implisit_ctc_revisi'] = $data_hb_c_revisi
                && $data_hk_c_revisi
                && $data_hb_c_1_revisi
                && $data_hk_c_1_revisi
                && isset($data_hk_c_revisi->$komp_id)
                && $data_hk_c_revisi->$komp_id != 0
                && isset($data_hb_c_1_revisi->$komp_id)
                && $data_hb_c_1_revisi->$komp_id != 0
                && isset($data_hk_c_1_revisi->$komp_id)
                && $data_hk_c_1_revisi->$komp_id != 0 ?
                (($data_hb_c_revisi->$komp_id / $data_hk_c_revisi->$komp_id * 100) - ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)) / ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100) * 100
                : null;

            $data[] = $row;
        }
        return $data;
    }
}
