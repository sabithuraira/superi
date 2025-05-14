<?php

namespace App\Http\Controllers;

use App\Komponen;
use App\SettingApp;
use App\Pdrb;
use App\PdrbFinal;
use App\Helpers\AssetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RevisiTotalController2 extends Controller
{
    public $list_wilayah;
    public $tahun_berlaku;
    public $triwulan_berlaku;
    public $list_periode = [];
    public $select_12pkrt = [];
    public $select_7pkrt = [];
    public $select_rilis = [];

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
        // dd($this->list_periode);
        $this->setSelectVariable();
    }

    private function setSelectVariable()
    {
        $str_sql_select = "";
        $list_komp = AssetData::getDetailKomponen();

        foreach ($list_komp as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $this->select_7pkrt = ['kode_kab', DB::raw(substr($str_sql_select, 0, -1))];

        //////////////////////

        $str_sql_select12 = "";
        $list_komp_12 = AssetData::$list_detail_komponen_12_pkrt;
        foreach ($list_komp_12 as $item) {
            $str_sql_select12 .= "SUM(" . $item['id'] . ") as " . $item['id'] . ",";
        }

        $this->select_12pkrt =  ['kode_kab', DB::raw(substr($str_sql_select12, 0, -1))];

        ///////////////////
        $str_sql_select_rilis = "";
        $list_komp_rilis = AssetData::$list_detail_komponen_rilis;
        foreach ($list_komp_rilis as $item) {
            $str_sql_select_rilis .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }

        $this->select_rilis =  ['kode_kab', DB::raw(substr($str_sql_select_rilis, 0, -1))];
    }

    public $list_tabel = [
        ['id' => '2.1', 'name' => 'Tabel 2.1. PDRB ADHB (juta Rp)'],
        ['id' => '2.2', 'name' => 'Tabel 2.2. PDRB ADHK (juta Rp)'],
        ['id' => '2.3', 'name' => 'Tabel 2.3. Distribusi Terhadap Provinsi (persen)'],
        ['id' => '2.4', 'name' => 'Tabel 2.4. Distribusi Komponen Terhadap PDRB ADHB (persen)'],
        ['id' => '2.5', 'name' => 'Tabel 2.5. Distribusi Komponen Terhadap PDRB ADHK (persen)'],
        ['id' => '2.6', 'name' => 'Tabel 2.6. Indeks Implisit'],
        ['id' => '2.7', 'name' => 'Tabel 2.7. Indeks Implisit Kumulatif'],
        ['id' => '2.8', 'name' => 'Tabel 2.8. Pertumbuhan PDRB (Q-TO-Q), (persen)'],
        ['id' => '2.9', 'name' => 'Tabel 2.9. Pertumbuhan PDRB (Y-ON-Y), (persen)'],
        ['id' => '2.10', 'name' => 'Tabel 2.10. Pertumbuhan PDRB (C-TO-C)'],
        ['id' => '2.11', 'name' => 'Tabel 2.11. Pertumbuhan Indeks Implisit PDRB (Q-TO-Q), (persen)'],
        ['id' => '2.12', 'name' => 'Tabel 2.12. Pertumbuhan Indeks Implisit PDRB (Y-ON-Y), (persen)'],
        ['id' => '2.13', 'name' => 'Tabel 2.13. Pertumbuhan Indeks Implisit PDRB (C-TO-C), (persen)'],
        ['id' => '2.14', 'name' => 'Tabel 2.14. Sumber Pertumbuhan Terhadap Komponen 17 Kabkot (Q-TO-Q), (persen)'],
        ['id' => '2.15', 'name' => 'Tabel 2.15. Sumber Pertumbuhan Terhadap Komponen 17 Kabkot (Y-ON-Y), (persen)'],
        ['id' => '2.16', 'name' => 'Tabel 2.16. Sumber Pertumbuhan Terhadap Komponen 17 Kabkot (C-TO-C), (persen)'],
        ['id' => '2.17', 'name' => 'Tabel 2.17. Sumber Pertumbuhan Terhadap PDRB Kabupaten/Kota/Provinsi (Q-TO-Q), (persen)'],
        ['id' => '2.18', 'name' => 'Tabel 2.18. Sumber Pertumbuhan Terhadap PDRB Kabupaten/Kota/Provinsi (Y-ON-Y), (persen)'],
        ['id' => '2.19', 'name' => 'Tabel 2.19. Sumber Pertumbuhan Terhadap PDRB Kabupaten/Kota/Provinsi (C-TO-C), (persen)'],
    ];

    /**
     * get last revisi data base on kab, tahun, q, adhk & status
     */
    public function get_q($total_or_prov, $thn, $adhk, $status_rilis)
    {
        $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
            ->when($total_or_prov == 1, function ($query) {
                return $query->where('kode_kab', '!=', "00");
            }, function ($query) {
                return $query->where('kode_kab', '=', "00");
            })
            ->where('tahun', $thn)
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status_rilis . '%')
            ->groupBy('kode_kab', 'q')
            ->get();
        return $rev;
    }

    public function get_data_cum($total_or_prov, $thn, $q, $adhk, $status_rilis, $rev)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);
        $data = Pdrb::select('kode_prov', DB::raw($str_sql_select))
            ->when($total_or_prov == 1, function ($query) {
                return $query->where('kode_kab', '!=', "00");
            }, function ($query) {
                return $query->where('kode_kab', '=', "00");
            })
            ->where('tahun', $thn)
            ->wherein('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status_rilis . '%')
            ->where(function ($query) use ($rev) {
                foreach ($rev as $r) {
                    $query->orWhere(function ($subquery) use ($r) {
                        $subquery->where('kode_kab', $r->kode_kab)
                            ->where('q', $r->q)
                            ->where('revisi_ke', $r->max_revisi);
                    });
                }
            })
            // ->orderby('revisi_ke', 'desc')
            ->groupBy('kode_prov')
            ->first();
        return $data;
    }

    public function rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select)
    {
        $data = [];
        $array_komp_filter = [];
        foreach ($komponen_filter as $item) {
            $array_komp_filter = array_merge($array_komp_filter, array_map('trim', explode(',', $item)));
        }
        $komponens = [];
        $list_detail_komponen = AssetData::getDetailKomponen();
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
        if ($id === '2.1') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis = $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi = $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis = $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi = $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis);
                    $kabkot_revisi = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi);
                    $prov_rilis = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis);
                    $prov_revisi = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis ? $kabkot_rilis->$komp_id : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi ? $kabkot_revisi->$komp_id : null;
                    $row[$periode . "prov_rilis"] = $prov_rilis ? $prov_rilis->$komp_id : null;
                    $row[$periode . "prov_revisi"] = $prov_revisi ? $prov_revisi->$komp_id : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.2') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi = $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis ? $kabkot_rilis->$komp_id : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi ? $kabkot_revisi->$komp_id : null;
                    $row[$periode . "prov_rilis"] = $prov_rilis ? $prov_rilis->$komp_id : null;
                    $row[$periode . "prov_revisi"] = $prov_revisi ? $prov_revisi->$komp_id : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.3') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis);
                    $kabkot_revisi =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi);
                    $prov_rilis =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis);
                    $prov_revisi =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi);

                    $row[$periode . "kabkot_rilis"]     = $kabkot_rilis && $prov_rilis && isset($prov_rilis->$komp_id) && $prov_rilis->$komp_id != 0 ?  $kabkot_rilis->$komp_id / $prov_rilis->$komp_id * 100 : null;
                    $row[$periode . "kabkot_revisi"]    = $kabkot_rilis && $prov_revisi && isset($prov_revisi->$komp_id) && $prov_revisi->$komp_id != 0 ? $kabkot_revisi->$komp_id / $prov_revisi->$komp_id * 100 : null;
                    $row[$periode . "prov_rilis"]       = $prov_rilis && $prov_rilis && isset($prov_rilis->$komp_id) && $prov_rilis->$komp_id != 0 ?  $prov_rilis->$komp_id / $prov_rilis->$komp_id * 100 : null;
                    $row[$periode . "prov_revisi"]      = $prov_revisi && $prov_revisi && isset($prov_revisi->$komp_id) && $prov_revisi->$komp_id != 0 ? $prov_revisi->$komp_id / $prov_revisi->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.4') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis);
                    $kabkot_revisi =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi);
                    $prov_rilis =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis);
                    $prov_revisi =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi);

                    $row[$periode . "kabkot_rilis"]     = $kabkot_rilis  && isset($kabkot_rilis->c_pdrb) && $kabkot_rilis->c_pdrb != 0 ?  $kabkot_rilis->$komp_id / $kabkot_rilis->c_pdrb * 100 : null;
                    $row[$periode . "kabkot_revisi"]    = $kabkot_revisi  && isset($kabkot_revisi->c_pdrb) && $kabkot_revisi->c_pdrb != 0 ?  $kabkot_revisi->$komp_id / $kabkot_revisi->c_pdrb * 100 : null;
                    $row[$periode . "prov_rilis"]       = $prov_rilis  && isset($prov_rilis->c_pdrb) && $prov_rilis->c_pdrb != 0 ?  $prov_rilis->$komp_id / $prov_rilis->c_pdrb * 100 : null;
                    $row[$periode . "prov_revisi"]      = $prov_revisi  && isset($prov_revisi->c_pdrb) && $prov_revisi->c_pdrb != 0 ?  $prov_revisi->$komp_id / $prov_revisi->c_pdrb * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.5') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);

                    $row[$periode . "kabkot_rilis"]     = $kabkot_rilis  && isset($kabkot_rilis->c_pdrb) && $kabkot_rilis->c_pdrb != 0 ?  $kabkot_rilis->$komp_id / $kabkot_rilis->c_pdrb * 100 : null;
                    $row[$periode . "kabkot_revisi"]    = $kabkot_revisi  && isset($kabkot_revisi->c_pdrb) && $kabkot_revisi->c_pdrb != 0 ?  $kabkot_revisi->$komp_id / $kabkot_revisi->c_pdrb * 100 : null;
                    $row[$periode . "prov_rilis"]       = $prov_rilis  && isset($prov_rilis->c_pdrb) && $prov_rilis->c_pdrb != 0 ?  $prov_rilis->$komp_id / $prov_rilis->c_pdrb * 100 : null;
                    $row[$periode . "prov_revisi"]      = $prov_revisi  && isset($prov_revisi->c_pdrb) && $prov_revisi->c_pdrb != 0 ?  $prov_revisi->$komp_id / $prov_revisi->c_pdrb * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.6') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis_hb =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_rilis_hk =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_hb =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_kabkot_revisi_hk =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_hb =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_rilis_hk =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_hb =    $this->get_q(0, $arr_periode[0], 1, "");
                    $q_prov_revisi_hk =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_hb =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis_hb);
                    $kabkot_rilis_hk =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis_hk);
                    $kabkot_revisi_hb =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi_hb);
                    $kabkot_revisi_hk =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi_hk);
                    $prov_rilis_hb =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis_hb);
                    $prov_rilis_hk =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis_hk);
                    $prov_revisi_hb =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi_hb);
                    $prov_revisi_hk =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi_hk);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis_hb && $kabkot_rilis_hk
                        && isset($kabkot_rilis_hk->$komp_id)
                        && $kabkot_rilis_hk->$komp_id != 0
                        ? $kabkot_rilis_hb->$komp_id / $kabkot_rilis_hk->$komp_id * 100 : null;

                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi_hb && $kabkot_revisi_hk
                        && isset($kabkot_revisi_hk->$komp_id)
                        && $kabkot_revisi_hk->$komp_id != 0
                        ? $kabkot_revisi_hb->$komp_id / $kabkot_revisi_hk->$komp_id * 100 : null;

                    $row[$periode . "prov_rilis"] = $prov_rilis_hb && $prov_rilis_hk
                        && isset($prov_rilis_hk->$komp_id)
                        && $prov_rilis_hk->$komp_id != 0
                        ? $prov_rilis_hb->$komp_id / $prov_rilis_hk->$komp_id * 100 : null;

                    $row[$periode . "prov_revisi"] = $prov_revisi_hb && $prov_revisi_hk
                        && isset($prov_revisi_hk->$komp_id)
                        && $prov_revisi_hk->$komp_id != 0
                        ? $prov_revisi_hb->$komp_id / $prov_revisi_hk->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.7') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $key => $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q = [];

                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $q_kabkot_rilis_hb =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_rilis_hk =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_hb =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_kabkot_revisi_hk =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_hb =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_rilis_hk =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_hb =    $this->get_q(0, $arr_periode[0], 1, "");
                    $q_prov_revisi_hk =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_hb =     $this->get_data_cum(1, $arr_periode[0], $q, 1, 3, $q_kabkot_rilis_hb);
                    $kabkot_rilis_hk =     $this->get_data_cum(1, $arr_periode[0], $q, 2, 3, $q_kabkot_rilis_hk);
                    $kabkot_revisi_hb =    $this->get_data_cum(1, $arr_periode[0], $q, 1, "", $q_kabkot_revisi_hb);
                    $kabkot_revisi_hk =    $this->get_data_cum(1, $arr_periode[0], $q, 2, "", $q_kabkot_revisi_hk);
                    $prov_rilis_hb =       $this->get_data_cum(0, $arr_periode[0], $q, 1, 3, $q_prov_rilis_hb);
                    $prov_rilis_hk =       $this->get_data_cum(0, $arr_periode[0], $q, 2, 3, $q_prov_rilis_hk);
                    $prov_revisi_hb =      $this->get_data_cum(0, $arr_periode[0], $q, 1, "", $q_prov_revisi_hb);
                    $prov_revisi_hk =      $this->get_data_cum(0, $arr_periode[0], $q, 2, "", $q_prov_revisi_hk);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis_hb && $kabkot_rilis_hk
                        && isset($kabkot_rilis_hk->$komp_id)
                        && $kabkot_rilis_hk->$komp_id != 0
                        ? $kabkot_rilis_hb->$komp_id / $kabkot_rilis_hk->$komp_id * 100 : null;

                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi_hb && $kabkot_revisi_hk
                        && isset($kabkot_revisi_hk->$komp_id)
                        && $kabkot_revisi_hk->$komp_id != 0
                        ? $kabkot_revisi_hb->$komp_id / $kabkot_revisi_hk->$komp_id * 100 : null;

                    $row[$periode . "prov_rilis"] = $prov_rilis_hb && $prov_rilis_hk
                        && isset($prov_rilis_hk->$komp_id)
                        && $prov_rilis_hk->$komp_id != 0
                        ? $prov_rilis_hb->$komp_id / $prov_rilis_hk->$komp_id * 100 : null;

                    $row[$periode . "prov_revisi"] = $prov_revisi_hb && $prov_revisi_hk
                        && isset($prov_revisi_hk->$komp_id)
                        && $prov_revisi_hk->$komp_id != 0
                        ? $prov_revisi_hb->$komp_id / $prov_revisi_hk->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.8') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y     = $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi_y);

                    if ($arr_periode[1] == 1) {
                        $q_kabkot_rilis_q     = $this->get_q(1, $arr_periode[0] - 1, 1, 3);
                        $q_kabkot_revisi_q    = $this->get_q(1, $arr_periode[0] - 1, 1, "");
                        $q_prov_rilis_q       = $this->get_q(0, $arr_periode[0] - 1, 1, 3);
                        $q_prov_revisi_q      = $this->get_q(0, $arr_periode[0] - 1, 1, "");

                        $kabkot_rilis_q   = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 1, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_q  = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 1, "", $q_kabkot_revisi_q);
                        $prov_rilis_q     = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 1, 3, $q_prov_rilis_q);
                        $prov_revisi_q    = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 1, "", $q_prov_revisi_q);
                    } else {
                        $q_kabkot_rilis_q     = $this->get_q(1, $arr_periode[0], 1, 3);
                        $q_kabkot_revisi_q    = $this->get_q(1, $arr_periode[0], 1, "");
                        $q_prov_rilis_q       = $this->get_q(0, $arr_periode[0], 1, 3);
                        $q_prov_revisi_q      = $this->get_q(0, $arr_periode[0], 1, "");

                        $kabkot_rilis_q   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 1, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_q  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 1, "", $q_kabkot_revisi_q);
                        $prov_rilis_q     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 1, 3, $q_prov_rilis_q);
                        $prov_revisi_q    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 1, "", $q_prov_revisi_q);
                    }
                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis_y && $kabkot_rilis_q && isset($kabkot_rilis_q->$komp_id) && $kabkot_rilis_q->$komp_id != 0 ? ($kabkot_rilis_y->$komp_id - $kabkot_rilis_q->$komp_id) / $kabkot_rilis_q->$komp_id * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi_y && $kabkot_revisi_q && isset($kabkot_revisi_q->$komp_id) && $kabkot_revisi_q->$komp_id != 0 ? ($kabkot_revisi_y->$komp_id - $kabkot_revisi_q->$komp_id) / $kabkot_revisi_q->$komp_id * 100 : null;
                    $row[$periode . "prov_rilis"] = $prov_rilis_y && $prov_rilis_q && isset($prov_rilis_q->$komp_id) && $prov_rilis_q->$komp_id != 0 ? ($prov_rilis_y->$komp_id - $prov_rilis_q->$komp_id) / $prov_rilis_q->$komp_id * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_revisi_y && $prov_revisi_q && isset($prov_revisi_q->$komp_id) && $prov_revisi_q->$komp_id != 0 ? ($prov_revisi_y->$komp_id - $prov_revisi_q->$komp_id) / $prov_revisi_q->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.9') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);


                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y      = $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi_y);

                    $q_kabkot_rilis_y_1     = $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_y_1    = $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_y_1       = $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_y_1     = $this->get_q(0, $arr_periode[0] - 1, 2, "");

                    $kabkot_rilis_y_1   = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y_1);
                    $kabkot_revisi_y_1  = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_kabkot_revisi_y_1);
                    $prov_rilis_y_1     = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_prov_rilis_y_1);
                    $prov_revisi_y_1    = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_prov_revisi_y_1);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis_y && $kabkot_rilis_y_1 && isset($kabkot_rilis_y_1->$komp_id) && $kabkot_rilis_y_1->$komp_id != 0 ? ($kabkot_rilis_y->$komp_id - $kabkot_rilis_y_1->$komp_id) / $kabkot_rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi_y && $kabkot_revisi_y_1 && isset($kabkot_revisi_y_1->$komp_id) && $kabkot_revisi_y_1->$komp_id != 0 ? ($kabkot_revisi_y->$komp_id - $kabkot_revisi_y_1->$komp_id) / $kabkot_revisi_y_1->$komp_id * 100 : null;
                    $row[$periode . "prov_rilis"] = $prov_rilis_y && $prov_rilis_y_1 && isset($prov_rilis_y_1->$komp_id) && $prov_rilis_y_1->$komp_id != 0 ? ($prov_rilis_y->$komp_id - $prov_rilis_y_1->$komp_id) / $prov_rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_revisi_y && $prov_revisi_y_1 && isset($prov_revisi_y_1->$komp_id) && $prov_revisi_y_1->$komp_id != 0 ? ($prov_revisi_y->$komp_id - $prov_revisi_y_1->$komp_id) / $prov_revisi_y_1->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.10') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y      = $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], $q, 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], $q, 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], $q, 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], $q, 2, "", $q_prov_revisi_y);

                    $q_kabkot_rilis_y_1     = $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_y_1    = $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_y_1       = $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_y_1     = $this->get_q(0, $arr_periode[0] - 1, 2, "");

                    $kabkot_rilis_y_1   = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, 3, $q_kabkot_rilis_y_1);
                    $kabkot_revisi_y_1  = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, "", $q_kabkot_revisi_y_1);
                    $prov_rilis_y_1     = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, 3, $q_prov_rilis_y_1);
                    $prov_revisi_y_1    = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, "", $q_prov_revisi_y_1);

                    $row[$periode . "kabkot_rilis"] = $kabkot_rilis_y && $kabkot_rilis_y_1 && isset($kabkot_rilis_y_1->$komp_id) && $kabkot_rilis_y_1->$komp_id != 0 ? ($kabkot_rilis_y->$komp_id - $kabkot_rilis_y_1->$komp_id) / $kabkot_rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_revisi_y && $kabkot_revisi_y_1 && isset($kabkot_revisi_y_1->$komp_id) && $kabkot_revisi_y_1->$komp_id != 0 ? ($kabkot_revisi_y->$komp_id - $kabkot_revisi_y_1->$komp_id) / $kabkot_revisi_y_1->$komp_id * 100 : null;
                    $row[$periode . "prov_rilis"] = $prov_rilis_y && $prov_rilis_y_1 && isset($prov_rilis_y_1->$komp_id) && $prov_rilis_y_1->$komp_id != 0 ? ($prov_rilis_y->$komp_id - $prov_rilis_y_1->$komp_id) / $prov_rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_revisi_y && $prov_revisi_y_1 && isset($prov_revisi_y_1->$komp_id) && $prov_revisi_y_1->$komp_id != 0 ? ($prov_revisi_y->$komp_id - $prov_revisi_y_1->$komp_id) / $prov_revisi_y_1->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.11') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);


                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis_hb =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis);
                    $kabkot_rilis_hk =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi_hb =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi);
                    $kabkot_revisi_hk =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis_hb =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis);
                    $prov_rilis_hk =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi_hb =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi);
                    $prov_revisi_hk =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);

                    if ($arr_periode[1] == 1) {
                        $q_kabkot_rilis_q =   $this->get_q(1, $arr_periode[0] - 1, 1, 3);
                        $q_kabkot_revisi_q =  $this->get_q(1, $arr_periode[0] - 1, 1, "");
                        $q_prov_rilis_q =     $this->get_q(0, $arr_periode[0] - 1, 1, 3);
                        $q_prov_revisi_q =    $this->get_q(0, $arr_periode[0] - 1, 1, "");

                        $kabkot_rilis_hb_q =     $this->get_data_cum(1, $arr_periode[0] - 1, [4], 1, 3, $q_kabkot_rilis_q);
                        $kabkot_rilis_hk_q =     $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_hb_q =    $this->get_data_cum(1, $arr_periode[0] - 1, [4], 1, "", $q_kabkot_revisi_q);
                        $kabkot_revisi_hk_q =    $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, "", $q_kabkot_revisi_q);
                        $prov_rilis_hb_q =       $this->get_data_cum(0, $arr_periode[0] - 1, [4], 1, 3, $q_prov_rilis_q);
                        $prov_rilis_hk_q =       $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, 3, $q_prov_rilis_q);
                        $prov_revisi_hb_q =      $this->get_data_cum(0, $arr_periode[0] - 1, [4], 1, "", $q_prov_revisi_q);
                        $prov_revisi_hk_q =      $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, "", $q_prov_revisi_q);
                    } else {

                        $q_kabkot_rilis_q =   $this->get_q(1, $arr_periode[0], 1, 3);
                        $q_kabkot_revisi_q =  $this->get_q(1, $arr_periode[0], 1, "");
                        $q_prov_rilis_q =     $this->get_q(0, $arr_periode[0], 1, 3);
                        $q_prov_revisi_q =    $this->get_q(0, $arr_periode[0], 1, "");

                        $kabkot_rilis_hb_q =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 1, 3, $q_kabkot_rilis_q);
                        $kabkot_rilis_hk_q =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_hb_q =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 1, "", $q_kabkot_revisi_q);
                        $kabkot_revisi_hk_q =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_kabkot_revisi_q);
                        $prov_rilis_hb_q =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 1, 3, $q_prov_rilis_q);
                        $prov_rilis_hk_q =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_prov_rilis_q);
                        $prov_revisi_hb_q =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 1, "", $q_prov_revisi_q);
                        $prov_revisi_hk_q =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_prov_revisi_q);
                    }

                    $kabkot_implisit_rilis      = $kabkot_rilis_hb      && $kabkot_rilis_hk     && isset($kabkot_rilis_hk->$komp_id)    && $kabkot_rilis_hk->$komp_id != 0      ? $kabkot_rilis_hb->$komp_id / $kabkot_rilis_hk->$komp_id * 100 : null;
                    $kabkot_implisit_rilis_1    = $kabkot_rilis_hb_q    && $kabkot_rilis_hk_q   && isset($kabkot_rilis_hk_q->$komp_id)  && $kabkot_rilis_hk_q->$komp_id != 0    ? $kabkot_rilis_hb_q->$komp_id / $kabkot_rilis_hk_q->$komp_id * 100 : null;
                    $kabkot_implisit_revisi     = $kabkot_revisi_hb     && $kabkot_revisi_hk    && isset($kabkot_revisi_hk->$komp_id)   && $kabkot_revisi_hk->$komp_id != 0     ? $kabkot_revisi_hb->$komp_id / $kabkot_revisi_hk->$komp_id * 100 : null;
                    $kabkot_implisit_revisi_1   = $kabkot_revisi_hb_q   && $kabkot_revisi_hk_q  && isset($kabkot_revisi_hk_q->$komp_id) && $kabkot_revisi_hk_q->$komp_id != 0   ? $kabkot_revisi_hb_q->$komp_id / $kabkot_revisi_hk_q->$komp_id * 100 : null;
                    $prov_implisit_rilis        = $prov_rilis_hb        && $prov_rilis_hk       && isset($prov_rilis_hk->$komp_id)      && $prov_rilis_hk->$komp_id != 0        ? $prov_rilis_hb->$komp_id / $prov_rilis_hk->$komp_id * 100 : null;
                    $prov_implisit_rilis_1      = $prov_rilis_hb_q      && $prov_rilis_hk_q     && isset($prov_rilis_hk_q->$komp_id)    && $prov_rilis_hk_q->$komp_id != 0      ? $prov_rilis_hb_q->$komp_id / $prov_rilis_hk_q->$komp_id * 100 : null;
                    $prov_implisit_revisi       = $prov_revisi_hb       && $prov_revisi_hk      && isset($prov_revisi_hk->$komp_id)     && $prov_revisi_hk->$komp_id != 0       ? $prov_revisi_hb->$komp_id / $prov_revisi_hk->$komp_id * 100 : null;
                    $prov_implisit_revisi_1     = $prov_revisi_hb_q     && $prov_revisi_hk_q    && isset($prov_revisi_hk_q->$komp_id)   && $prov_revisi_hk_q->$komp_id != 0     ? $prov_revisi_hb_q->$komp_id / $prov_revisi_hk_q->$komp_id * 100 : null;

                    $row[$periode . "kabkot_rilis"] = $kabkot_implisit_rilis && $kabkot_implisit_rilis_1 && $kabkot_implisit_rilis_1 != 0 ? ($kabkot_implisit_rilis - $kabkot_implisit_rilis_1) / $kabkot_implisit_rilis_1 * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_implisit_revisi && $kabkot_implisit_revisi_1 && $kabkot_implisit_revisi_1 != 0 ? ($kabkot_implisit_revisi - $kabkot_implisit_revisi_1) / $kabkot_implisit_revisi_1 * 100 : null;;
                    $row[$periode . "prov_rilis"] = $prov_implisit_rilis && $prov_implisit_rilis_1 && $prov_implisit_rilis_1 != 0 ? ($prov_implisit_rilis - $prov_implisit_rilis_1) / $prov_implisit_rilis_1 * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_implisit_revisi && $prov_implisit_revisi_1 && $prov_implisit_revisi_1 != 0 ? ($prov_implisit_revisi - $prov_implisit_revisi_1) / $prov_implisit_revisi_1 * 100 : null;;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.12') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis_hb =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_kabkot_rilis);
                    $kabkot_rilis_hk =     $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi_hb =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 1, "", $q_kabkot_revisi);
                    $kabkot_revisi_hk =    $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis_hb =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_prov_rilis);
                    $prov_rilis_hk =       $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi_hb =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 1, "", $q_prov_revisi);
                    $prov_revisi_hk =      $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);

                    $q_kabkot_rilis_1 =   $this->get_q(1, $arr_periode[0] - 1, 1, 3);
                    $q_kabkot_revisi_1 =  $this->get_q(1, $arr_periode[0] - 1, 1, "");
                    $q_prov_rilis_1 =     $this->get_q(0, $arr_periode[0] - 1, 1, 3);
                    $q_prov_revisi_1 =    $this->get_q(0, $arr_periode[0] - 1, 1, "");

                    $kabkot_rilis_hb_1 =     $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 1, 3, $q_kabkot_rilis_1);
                    $kabkot_rilis_hk_1 =     $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_kabkot_rilis_1);
                    $kabkot_revisi_hb_1 =    $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 1, "", $q_kabkot_revisi_1);
                    $kabkot_revisi_hk_1 =    $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_kabkot_revisi_1);
                    $prov_rilis_hb_1 =       $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 1, 3, $q_prov_rilis_1);
                    $prov_rilis_hk_1 =       $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_prov_rilis_1);
                    $prov_revisi_hb_1 =      $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 1, "", $q_prov_revisi_1);
                    $prov_revisi_hk_1 =      $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_prov_revisi_1);

                    $kabkot_implisit_rilis      = $kabkot_rilis_hb      && $kabkot_rilis_hk     && isset($kabkot_rilis_hk->$komp_id)    && $kabkot_rilis_hk->$komp_id != 0      ? $kabkot_rilis_hb->$komp_id / $kabkot_rilis_hk->$komp_id * 100 : null;
                    $kabkot_implisit_rilis_1    = $kabkot_rilis_hb_1    && $kabkot_rilis_hk_1   && isset($kabkot_rilis_hk_1->$komp_id)  && $kabkot_rilis_hk_1->$komp_id != 0    ? $kabkot_rilis_hb_1->$komp_id / $kabkot_rilis_hk_1->$komp_id * 100 : null;
                    $kabkot_implisit_revisi     = $kabkot_revisi_hb     && $kabkot_revisi_hk    && isset($kabkot_revisi_hk->$komp_id)   && $kabkot_revisi_hk->$komp_id != 0     ? $kabkot_revisi_hb->$komp_id / $kabkot_revisi_hk->$komp_id * 100 : null;
                    $kabkot_implisit_revisi_1   = $kabkot_revisi_hb_1   && $kabkot_revisi_hk_1  && isset($kabkot_revisi_hk_1->$komp_id) && $kabkot_revisi_hk_1->$komp_id != 0   ? $kabkot_revisi_hb_1->$komp_id / $kabkot_revisi_hk_1->$komp_id * 100 : null;
                    $prov_implisit_rilis        = $prov_rilis_hb        && $prov_rilis_hk       && isset($prov_rilis_hk->$komp_id)      && $prov_rilis_hk->$komp_id != 0        ? $prov_rilis_hb->$komp_id / $prov_rilis_hk->$komp_id * 100 : null;
                    $prov_implisit_rilis_1      = $prov_rilis_hb_1      && $prov_rilis_hk_1     && isset($prov_rilis_hk_1->$komp_id)    && $prov_rilis_hk_1->$komp_id != 0      ? $prov_rilis_hb_1->$komp_id / $prov_rilis_hk_1->$komp_id * 100 : null;
                    $prov_implisit_revisi       = $prov_revisi_hb       && $prov_revisi_hk      && isset($prov_revisi_hk->$komp_id)     && $prov_revisi_hk->$komp_id != 0       ? $prov_revisi_hb->$komp_id / $prov_revisi_hk->$komp_id * 100 : null;
                    $prov_implisit_revisi_1     = $prov_revisi_hb_1     && $prov_revisi_hk_1    && isset($prov_revisi_hk_1->$komp_id)   && $prov_revisi_hk_1->$komp_id != 0     ? $prov_revisi_hb_1->$komp_id / $prov_revisi_hk_1->$komp_id * 100 : null;

                    $row[$periode . "kabkot_rilis"] = $kabkot_implisit_rilis && $kabkot_implisit_rilis_1 && $kabkot_implisit_rilis_1 != 0 ? ($kabkot_implisit_rilis - $kabkot_implisit_rilis_1) / $kabkot_implisit_rilis_1 * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_implisit_revisi && $kabkot_implisit_revisi_1 && $kabkot_implisit_revisi_1 != 0 ? ($kabkot_implisit_revisi - $kabkot_implisit_revisi_1) / $kabkot_implisit_revisi_1 * 100 : null;
                    $row[$periode . "prov_rilis"] = $prov_implisit_rilis && $prov_implisit_rilis_1 && $prov_implisit_rilis_1 != 0 ? ($prov_implisit_rilis - $prov_implisit_rilis_1) / $prov_implisit_rilis_1 * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_implisit_revisi && $prov_implisit_revisi_1 && $prov_implisit_revisi_1 != 0 ? ($prov_implisit_revisi - $prov_implisit_revisi_1) / $prov_implisit_revisi_1 * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.13') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }


                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 1, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 1, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 1, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 1, "");

                    $kabkot_rilis_hb =     $this->get_data_cum(1, $arr_periode[0], $q, 1, 3, $q_kabkot_rilis);
                    $kabkot_rilis_hk =     $this->get_data_cum(1, $arr_periode[0], $q, 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi_hb =    $this->get_data_cum(1, $arr_periode[0], $q, 1, "", $q_kabkot_revisi);
                    $kabkot_revisi_hk =    $this->get_data_cum(1, $arr_periode[0], $q, 2, "", $q_kabkot_revisi);
                    $prov_rilis_hb =       $this->get_data_cum(0, $arr_periode[0], $q, 1, 3, $q_prov_rilis);
                    $prov_rilis_hk =       $this->get_data_cum(0, $arr_periode[0], $q, 2, 3, $q_prov_rilis);
                    $prov_revisi_hb =      $this->get_data_cum(0, $arr_periode[0], $q, 1, "", $q_prov_revisi);
                    $prov_revisi_hk =      $this->get_data_cum(0, $arr_periode[0], $q, 2, "", $q_prov_revisi);

                    $q_kabkot_rilis_1 =   $this->get_q(1, $arr_periode[0] - 1, 1, 3);
                    $q_kabkot_revisi_1 =  $this->get_q(1, $arr_periode[0] - 1, 1, "");
                    $q_prov_rilis_1 =     $this->get_q(0, $arr_periode[0] - 1, 1, 3);
                    $q_prov_revisi_1 =    $this->get_q(0, $arr_periode[0] - 1, 1, "");

                    $kabkot_rilis_hb_1 =     $this->get_data_cum(1, $arr_periode[0] - 1, $q, 1, 3, $q_kabkot_rilis_1);
                    $kabkot_rilis_hk_1 =     $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, 3, $q_kabkot_rilis_1);
                    $kabkot_revisi_hb_1 =    $this->get_data_cum(1, $arr_periode[0] - 1, $q, 1, "", $q_kabkot_revisi_1);
                    $kabkot_revisi_hk_1 =    $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, "", $q_kabkot_revisi_1);
                    $prov_rilis_hb_1 =       $this->get_data_cum(0, $arr_periode[0] - 1, $q, 1, 3, $q_prov_rilis_1);
                    $prov_rilis_hk_1 =       $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, 3, $q_prov_rilis_1);
                    $prov_revisi_hb_1 =      $this->get_data_cum(0, $arr_periode[0] - 1, $q, 1, "", $q_prov_revisi_1);
                    $prov_revisi_hk_1 =      $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, "", $q_prov_revisi_1);

                    $kabkot_implisit_rilis      = $kabkot_rilis_hb      && $kabkot_rilis_hk     && isset($kabkot_rilis_hk->$komp_id)    && $kabkot_rilis_hk->$komp_id != 0      ? $kabkot_rilis_hb->$komp_id / $kabkot_rilis_hk->$komp_id * 100 : null;
                    $kabkot_implisit_rilis_1    = $kabkot_rilis_hb_1    && $kabkot_rilis_hk_1   && isset($kabkot_rilis_hk_1->$komp_id)  && $kabkot_rilis_hk_1->$komp_id != 0    ? $kabkot_rilis_hb_1->$komp_id / $kabkot_rilis_hk_1->$komp_id * 100 : null;
                    $kabkot_implisit_revisi     = $kabkot_revisi_hb     && $kabkot_revisi_hk    && isset($kabkot_revisi_hk->$komp_id)   && $kabkot_revisi_hk->$komp_id != 0     ? $kabkot_revisi_hb->$komp_id / $kabkot_revisi_hk->$komp_id * 100 : null;
                    $kabkot_implisit_revisi_1   = $kabkot_revisi_hb_1   && $kabkot_revisi_hk_1  && isset($kabkot_revisi_hk_1->$komp_id) && $kabkot_revisi_hk_1->$komp_id != 0   ? $kabkot_revisi_hb_1->$komp_id / $kabkot_revisi_hk_1->$komp_id * 100 : null;
                    $prov_implisit_rilis        = $prov_rilis_hb        && $prov_rilis_hk       && isset($prov_rilis_hk->$komp_id)      && $prov_rilis_hk->$komp_id != 0        ? $prov_rilis_hb->$komp_id / $prov_rilis_hk->$komp_id * 100 : null;
                    $prov_implisit_rilis_1      = $prov_rilis_hb_1      && $prov_rilis_hk_1     && isset($prov_rilis_hk_1->$komp_id)    && $prov_rilis_hk_1->$komp_id != 0      ? $prov_rilis_hb_1->$komp_id / $prov_rilis_hk_1->$komp_id * 100 : null;
                    $prov_implisit_revisi       = $prov_revisi_hb       && $prov_revisi_hk      && isset($prov_revisi_hk->$komp_id)     && $prov_revisi_hk->$komp_id != 0       ? $prov_revisi_hb->$komp_id / $prov_revisi_hk->$komp_id * 100 : null;
                    $prov_implisit_revisi_1     = $prov_revisi_hb_1     && $prov_revisi_hk_1    && isset($prov_revisi_hk_1->$komp_id)   && $prov_revisi_hk_1->$komp_id != 0     ? $prov_revisi_hb_1->$komp_id / $prov_revisi_hk_1->$komp_id * 100 : null;

                    $row[$periode . "kabkot_rilis"] = $kabkot_implisit_rilis && $kabkot_implisit_rilis_1 && $kabkot_implisit_rilis_1 != 0 ? ($kabkot_implisit_rilis - $kabkot_implisit_rilis_1) / $kabkot_implisit_rilis_1 * 100 : null;
                    $row[$periode . "kabkot_revisi"] = $kabkot_implisit_revisi && $kabkot_implisit_revisi_1 && $kabkot_implisit_revisi_1 != 0 ? ($kabkot_implisit_revisi - $kabkot_implisit_revisi_1) / $kabkot_implisit_revisi_1 * 100 : null;
                    $row[$periode . "prov_rilis"] = $prov_implisit_rilis && $prov_implisit_rilis_1 && $prov_implisit_rilis_1 != 0 ? ($prov_implisit_rilis - $prov_implisit_rilis_1) / $prov_implisit_rilis_1 * 100 : null;
                    $row[$periode . "prov_revisi"] = $prov_implisit_revisi && $prov_implisit_revisi_1 && $prov_implisit_revisi_1 != 0 ? ($prov_implisit_revisi - $prov_implisit_revisi_1) / $prov_implisit_revisi_1 * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.14') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y     = $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi_y);

                    if ($arr_periode[1] == 1) {
                        $q_kabkot_rilis_q     = $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                        $q_kabkot_revisi_q    = $this->get_q(1, $arr_periode[0] - 1, 2, "");
                        $q_prov_rilis_q       = $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                        $q_prov_revisi_q      = $this->get_q(0, $arr_periode[0] - 1, 2, "");

                        $kabkot_rilis_q   = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_q  = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, "", $q_kabkot_revisi_q);
                        $prov_rilis_q     = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, 3, $q_prov_rilis_q);
                        $prov_revisi_q    = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, "", $q_prov_revisi_q);
                    } else {
                        $q_kabkot_rilis_q     = $this->get_q(1, $arr_periode[0], 2, 3);
                        $q_kabkot_revisi_q    = $this->get_q(1, $arr_periode[0], 2, "");
                        $q_prov_rilis_q       = $this->get_q(0, $arr_periode[0], 2, 3);
                        $q_prov_revisi_q      = $this->get_q(0, $arr_periode[0], 2, "");

                        $kabkot_rilis_q   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_kabkot_rilis_q);
                        $kabkot_revisi_q  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_kabkot_revisi_q);
                        $prov_rilis_q     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_prov_rilis_q);
                        $prov_revisi_q    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_prov_revisi_q);
                    }

                    $laju_pert_rilis =  $prov_rilis_y && $prov_rilis_q && isset($prov_rilis_q->c_pdrb) && $prov_rilis_q->c_pdrb != 0 ? ($prov_rilis_y->c_pdrb - $prov_rilis_q->c_pdrb) / $prov_rilis_q->c_pdrb * 100 : null;
                    $laju_pert_rev =  $prov_revisi_y && $prov_revisi_q && isset($prov_revisi_q->c_pdrb) && $prov_revisi_q->c_pdrb != 0 ? ($prov_revisi_y->c_pdrb - $prov_revisi_q->c_pdrb) / $prov_revisi_q->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis_y
                        && $kabkot_rilis_q
                        && $laju_pert_rilis
                        && isset($kabkot_rilis_y->$komp_id)
                        && isset($kabkot_rilis_q->$komp_id)
                        && isset($kabkot_rilis_y->c_pdrb)
                        && isset($kabkot_rilis_q->c_pdrb)
                        && ($kabkot_rilis_y->c_pdrb - $kabkot_rilis_q->c_pdrb) != 0
                        ? (($kabkot_rilis_y->$komp_id - $kabkot_rilis_q->$komp_id) / ($kabkot_rilis_y->c_pdrb - $kabkot_rilis_q->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi_y
                        && $kabkot_revisi_q
                        && $laju_pert_rev
                        && isset($kabkot_revisi_y->$komp_id)
                        && isset($kabkot_revisi_q->$komp_id)
                        && isset($kabkot_revisi_y->c_pdrb)
                        && isset($kabkot_revisi_q->c_pdrb)
                        && ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_q->c_pdrb) != 0                        ?
                        (($kabkot_revisi_y->$komp_id - $kabkot_revisi_q->$komp_id) / ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_q->c_pdrb)) * $laju_pert_rev : null;


                    $row[$periode . 'prov_rilis'] = $prov_rilis_y
                        && $prov_rilis_q
                        && $laju_pert_rilis
                        && isset($prov_rilis_y->$komp_id)
                        && isset($prov_rilis_q->$komp_id)
                        && isset($prov_rilis_y->c_pdrb)
                        && isset($prov_rilis_q->c_pdrb)
                        && ($prov_rilis_y->c_pdrb - $prov_rilis_q->c_pdrb) != 0
                        ? (($prov_rilis_y->$komp_id - $prov_rilis_q->$komp_id) / ($prov_rilis_y->c_pdrb - $prov_rilis_q->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi_y
                        && $prov_revisi_q
                        && $laju_pert_rev
                        && isset($prov_revisi_y->$komp_id)
                        && isset($prov_revisi_q->$komp_id)
                        && isset($prov_revisi_y->c_pdrb)
                        && isset($prov_revisi_q->c_pdrb)
                        && ($prov_revisi_y->c_pdrb - $prov_revisi_q->c_pdrb) != 0                        ?
                        (($prov_revisi_y->$komp_id - $prov_revisi_q->$komp_id) / ($prov_revisi_y->c_pdrb - $prov_revisi_q->c_pdrb)) * $laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.15') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y      = $this->get_q(0, $arr_periode[0], 2, "");
                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi_y);

                    $q_kabkot_rilis_y_1     = $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_y_1    = $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_y_1       = $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_y_1      = $this->get_q(0, $arr_periode[0] - 1, 2, "");
                    $kabkot_rilis_y_1   = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_kabkot_rilis_y_1);
                    $kabkot_revisi_y_1  = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_kabkot_revisi_y_1);
                    $prov_rilis_y_1     = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_prov_rilis_y_1);
                    $prov_revisi_y_1    = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_prov_revisi_y_1);

                    $laju_pert_rilis =  $prov_rilis_y && $prov_rilis_y_1 && isset($prov_rilis_y_1->c_pdrb) && $prov_rilis_y_1->c_pdrb != 0 ? ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb) / $prov_rilis_y_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $prov_revisi_y && $prov_revisi_y_1 && isset($prov_revisi_y_1->c_pdrb) && $prov_revisi_y_1->c_pdrb != 0 ? ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb) / $prov_revisi_y_1->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis_y
                        && $q_kabkot_rilis_y_1
                        && $laju_pert_rilis
                        && isset($kabkot_rilis_y->$komp_id)
                        && isset($q_kabkot_rilis_y_1->$komp_id)
                        && isset($kabkot_rilis_y->c_pdrb)
                        && isset($q_kabkot_rilis_y_1->c_pdrb)
                        && ($kabkot_rilis_y->c_pdrb - $q_kabkot_rilis_y_1->c_pdrb) != 0
                        ? (($kabkot_rilis_y->$komp_id - $q_kabkot_rilis_y_1->$komp_id) / ($kabkot_rilis_y->c_pdrb - $q_kabkot_rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi_y
                        && $kabkot_revisi_y_1
                        && $laju_pert_rev
                        && isset($kabkot_revisi_y->$komp_id)
                        && isset($kabkot_revisi_y_1->$komp_id)
                        && isset($kabkot_revisi_y->c_pdrb)
                        && isset($kabkot_revisi_y_1->c_pdrb)
                        && ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_y_1->c_pdrb) != 0                        ?
                        (($kabkot_revisi_y->$komp_id - $kabkot_revisi_y_1->$komp_id) / ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_y_1->c_pdrb)) * $laju_pert_rev : null;


                    $row[$periode . 'prov_rilis'] = $prov_rilis_y
                        && $prov_rilis_y_1
                        && $laju_pert_rilis
                        && isset($prov_rilis_y->$komp_id)
                        && isset($prov_rilis_y_1->$komp_id)
                        && isset($prov_rilis_y->c_pdrb)
                        && isset($prov_rilis_y_1->c_pdrb)
                        && ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb) != 0
                        ? (($prov_rilis_y->$komp_id - $prov_rilis_y_1->$komp_id) / ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi_y
                        && $prov_revisi_y_1
                        && $laju_pert_rev
                        && isset($prov_revisi_y->$komp_id)
                        && isset($prov_revisi_y_1->$komp_id)
                        && isset($prov_revisi_y->c_pdrb)
                        && isset($prov_revisi_y_1->c_pdrb)
                        && ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb) != 0                        ?
                        (($prov_revisi_y->$komp_id - $prov_revisi_y_1->$komp_id) / ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb)) * $laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.16') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $q_kabkot_rilis_y     = $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi_y    = $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis_y       = $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi_y      = $this->get_q(0, $arr_periode[0], 2, "");
                    $kabkot_rilis_y   = $this->get_data_cum(1, $arr_periode[0], $q, 2, 3, $q_kabkot_rilis_y);
                    $kabkot_revisi_y  = $this->get_data_cum(1, $arr_periode[0], $q, 2, "", $q_kabkot_revisi_y);
                    $prov_rilis_y     = $this->get_data_cum(0, $arr_periode[0], $q, 2, 3, $q_prov_rilis_y);
                    $prov_revisi_y    = $this->get_data_cum(0, $arr_periode[0], $q, 2, "", $q_prov_revisi_y);

                    $q_kabkot_rilis_y_1     = $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_y_1    = $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_y_1       = $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_y_1      = $this->get_q(0, $arr_periode[0] - 1, 2, "");
                    $kabkot_rilis_y_1   = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, 3, $q_kabkot_rilis_y_1);
                    $kabkot_revisi_y_1  = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, "", $q_kabkot_revisi_y_1);
                    $prov_rilis_y_1     = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, 3, $q_prov_rilis_y_1);
                    $prov_revisi_y_1    = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, "", $q_prov_revisi_y_1);

                    $laju_pert_rilis =  $prov_rilis_y && $prov_rilis_y_1 && isset($prov_rilis_y_1->c_pdrb) && $prov_rilis_y_1->c_pdrb != 0 ? ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb) / $prov_rilis_y_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $prov_revisi_y && $prov_revisi_y_1 && isset($prov_revisi_y_1->c_pdrb) && $prov_revisi_y_1->c_pdrb != 0 ? ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb) / $prov_revisi_y_1->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis_y
                        && $q_kabkot_rilis_y_1
                        && $laju_pert_rilis
                        && isset($kabkot_rilis_y->$komp_id)
                        && isset($q_kabkot_rilis_y_1->$komp_id)
                        && isset($kabkot_rilis_y->c_pdrb)
                        && isset($q_kabkot_rilis_y_1->c_pdrb)
                        && ($kabkot_rilis_y->c_pdrb - $q_kabkot_rilis_y_1->c_pdrb) != 0
                        ? (($kabkot_rilis_y->$komp_id - $q_kabkot_rilis_y_1->$komp_id) / ($kabkot_rilis_y->c_pdrb - $q_kabkot_rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi_y
                        && $kabkot_revisi_y_1
                        && $laju_pert_rev
                        && isset($kabkot_revisi_y->$komp_id)
                        && isset($kabkot_revisi_y_1->$komp_id)
                        && isset($kabkot_revisi_y->c_pdrb)
                        && isset($kabkot_revisi_y_1->c_pdrb)
                        && ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_y_1->c_pdrb) != 0                        ?
                        (($kabkot_revisi_y->$komp_id - $kabkot_revisi_y_1->$komp_id) / ($kabkot_revisi_y->c_pdrb - $kabkot_revisi_y_1->c_pdrb)) * $laju_pert_rev : null;


                    $row[$periode . 'prov_rilis'] = $prov_rilis_y
                        && $prov_rilis_y_1
                        && $laju_pert_rilis
                        && isset($prov_rilis_y->$komp_id)
                        && isset($prov_rilis_y_1->$komp_id)
                        && isset($prov_rilis_y->c_pdrb)
                        && isset($prov_rilis_y_1->c_pdrb)
                        && ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb) != 0
                        ? (($prov_rilis_y->$komp_id - $prov_rilis_y_1->$komp_id) / ($prov_rilis_y->c_pdrb - $prov_rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi_y
                        && $prov_revisi_y_1
                        && $laju_pert_rev
                        && isset($prov_revisi_y->$komp_id)
                        && isset($prov_revisi_y_1->$komp_id)
                        && isset($prov_revisi_y->c_pdrb)
                        && isset($prov_revisi_y_1->c_pdrb)
                        && ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb) != 0                        ?
                        (($prov_revisi_y->$komp_id - $prov_revisi_y_1->$komp_id) / ($prov_revisi_y->c_pdrb - $prov_revisi_y_1->c_pdrb)) * $laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.17') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi   = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);


                    if ($arr_periode[1] == 1) {
                        $q_kabkot_rilis_1 =   $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                        $q_kabkot_revisi_1 =  $this->get_q(1, $arr_periode[0] - 1, 2, "");
                        $q_prov_rilis_1 =     $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                        $q_prov_revisi_1 =    $this->get_q(0, $arr_periode[0] - 1, 2, "");

                        $kabkot_rilis_1  = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, 3, $q_kabkot_rilis_1);
                        $kabkot_revisi_1 = $this->get_data_cum(1, $arr_periode[0] - 1, [4], 2, "", $q_kabkot_revisi_1);
                        $prov_rilis_1    = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, 3, $q_prov_rilis_1);
                        $prov_revisi_1   = $this->get_data_cum(0, $arr_periode[0] - 1, [4], 2, "", $q_prov_revisi_1);
                    } else {
                        $kabkot_rilis_1  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_kabkot_rilis);
                        $kabkot_revisi_1 = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_kabkot_revisi);
                        $prov_rilis_1    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_prov_rilis);
                        $prov_revisi_1   = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_prov_revisi);
                    }
                    $kabkot_laju_pert_rilis =  $kabkot_rilis && $kabkot_rilis_1 && isset($kabkot_rilis_1->c_pdrb) && $kabkot_rilis_1->c_pdrb != 0 ? ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) / $kabkot_rilis_1->c_pdrb * 100 : null;
                    $kabkot_laju_pert_rev =  $kabkot_revisi && $kabkot_revisi_1 && isset($kabkot_revisi_1->c_pdrb) && $kabkot_revisi_1->c_pdrb != 0 ? ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) / $kabkot_revisi_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rilis =  $prov_rilis && $prov_rilis_1 && isset($prov_rilis_1->c_pdrb) && $prov_rilis_1->c_pdrb != 0 ? ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) / $prov_rilis_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rev =  $prov_revisi && $prov_revisi_1 && isset($prov_revisi_1->c_pdrb) && $prov_revisi_1->c_pdrb != 0 ? ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) / $prov_revisi_1->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis
                        && $kabkot_rilis_1
                        && $kabkot_laju_pert_rilis
                        && isset($kabkot_rilis->$komp_id)
                        && isset($kabkot_rilis_1->$komp_id)
                        && isset($kabkot_rilis->c_pdrb)
                        && isset($kabkot_rilis_1->c_pdrb)
                        && ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) != 0                        ?
                        (($kabkot_rilis->$komp_id - $kabkot_rilis_1->$komp_id) / ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb)) * $kabkot_laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi
                        && $kabkot_revisi_1
                        && $kabkot_laju_pert_rev
                        && isset($kabkot_revisi->$komp_id)
                        && isset($kabkot_revisi_1->$komp_id)
                        && isset($kabkot_revisi->c_pdrb)
                        && isset($kabkot_revisi_1->c_pdrb)
                        && ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) != 0                        ?
                        (($kabkot_revisi->$komp_id - $kabkot_revisi_1->$komp_id) / ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb)) * $kabkot_laju_pert_rev : null;

                    $row[$periode . 'prov_rilis'] = $prov_rilis
                        && $prov_rilis_1
                        && $prov_laju_pert_rilis
                        && isset($prov_rilis->$komp_id)
                        && isset($prov_rilis_1->$komp_id)
                        && isset($prov_rilis->c_pdrb)
                        && isset($prov_rilis_1->c_pdrb)
                        && ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) != 0                        ?
                        (($prov_rilis->$komp_id - $prov_rilis_1->$komp_id) / ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb)) * $prov_laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi
                        && $prov_revisi_1
                        && $prov_laju_pert_rev
                        && isset($prov_revisi->$komp_id)
                        && isset($prov_revisi_1->$komp_id)
                        && isset($prov_revisi->c_pdrb)
                        && isset($prov_revisi_1->c_pdrb)
                        && ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) != 0                        ?
                        (($prov_revisi->$komp_id - $prov_revisi_1->$komp_id) / ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb)) * $prov_laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.18') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis  = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi = $this->get_data_cum(1, $arr_periode[0], [$arr_periode[1]], 2, "", $q_kabkot_revisi);
                    $prov_rilis    = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_prov_rilis);
                    $prov_revisi   = $this->get_data_cum(0, $arr_periode[0], [$arr_periode[1]], 2, "", $q_prov_revisi);

                    $q_kabkot_rilis_1 =   $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_1 =  $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_1 =     $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_1 =    $this->get_q(0, $arr_periode[0] - 1, 2, "");

                    $kabkot_rilis_1  = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_kabkot_rilis_1);
                    $kabkot_revisi_1 = $this->get_data_cum(1, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_kabkot_revisi_1);
                    $prov_rilis_1    = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_prov_rilis_1);
                    $prov_revisi_1   = $this->get_data_cum(0, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_prov_revisi_1);


                    $kabkot_laju_pert_rilis =  $kabkot_rilis && $kabkot_rilis_1 && isset($kabkot_rilis_1->c_pdrb) && $kabkot_rilis_1->c_pdrb != 0 ? ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) / $kabkot_rilis_1->c_pdrb * 100 : null;
                    $kabkot_laju_pert_rev =  $kabkot_revisi && $kabkot_revisi_1 && isset($kabkot_revisi_1->c_pdrb) && $kabkot_revisi_1->c_pdrb != 0 ? ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) / $kabkot_revisi_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rilis =  $prov_rilis && $prov_rilis_1 && isset($prov_rilis_1->c_pdrb) && $prov_rilis_1->c_pdrb != 0 ? ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) / $prov_rilis_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rev =  $prov_revisi && $prov_revisi_1 && isset($prov_revisi_1->c_pdrb) && $prov_revisi_1->c_pdrb != 0 ? ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) / $prov_revisi_1->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis
                        && $kabkot_rilis_1
                        && $kabkot_laju_pert_rilis
                        && isset($kabkot_rilis->$komp_id)
                        && isset($kabkot_rilis_1->$komp_id)
                        && isset($kabkot_rilis->c_pdrb)
                        && isset($kabkot_rilis_1->c_pdrb)
                        && ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) != 0                        ?
                        (($kabkot_rilis->$komp_id - $kabkot_rilis_1->$komp_id) / ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb)) * $kabkot_laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi
                        && $kabkot_revisi_1
                        && $kabkot_laju_pert_rev
                        && isset($kabkot_revisi->$komp_id)
                        && isset($kabkot_revisi_1->$komp_id)
                        && isset($kabkot_revisi->c_pdrb)
                        && isset($kabkot_revisi_1->c_pdrb)
                        && ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) != 0                        ?
                        (($kabkot_revisi->$komp_id - $kabkot_revisi_1->$komp_id) / ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb)) * $kabkot_laju_pert_rev : null;

                    $row[$periode . 'prov_rilis'] = $prov_rilis
                        && $prov_rilis_1
                        && $prov_laju_pert_rilis
                        && isset($prov_rilis->$komp_id)
                        && isset($prov_rilis_1->$komp_id)
                        && isset($prov_rilis->c_pdrb)
                        && isset($prov_rilis_1->c_pdrb)
                        && ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) != 0                        ?
                        (($prov_rilis->$komp_id - $prov_rilis_1->$komp_id) / ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb)) * $prov_laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi
                        && $prov_revisi_1
                        && $prov_laju_pert_rev
                        && isset($prov_revisi->$komp_id)
                        && isset($prov_revisi_1->$komp_id)
                        && isset($prov_revisi->c_pdrb)
                        && isset($prov_revisi_1->c_pdrb)
                        && ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) != 0                        ?
                        (($prov_revisi->$komp_id - $prov_revisi_1->$komp_id) / ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb)) * $prov_laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.19') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['id'],
                    'name' => $komponen['name'],
                ];
                $komp_id = str_replace(".", "", $komponen['id']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $q_kabkot_rilis =   $this->get_q(1, $arr_periode[0], 2, 3);
                    $q_kabkot_revisi =  $this->get_q(1, $arr_periode[0], 2, "");
                    $q_prov_rilis =     $this->get_q(0, $arr_periode[0], 2, 3);
                    $q_prov_revisi =    $this->get_q(0, $arr_periode[0], 2, "");

                    $kabkot_rilis  = $this->get_data_cum(1, $arr_periode[0], $q, 2, 3, $q_kabkot_rilis);
                    $kabkot_revisi = $this->get_data_cum(1, $arr_periode[0], $q, 2, "", $q_kabkot_revisi);
                    $prov_rilis    = $this->get_data_cum(0, $arr_periode[0], $q, 2, 3, $q_prov_rilis);
                    $prov_revisi   = $this->get_data_cum(0, $arr_periode[0], $q, 2, "", $q_prov_revisi);


                    $q_kabkot_rilis_1 =   $this->get_q(1, $arr_periode[0] - 1, 2, 3);
                    $q_kabkot_revisi_1 =  $this->get_q(1, $arr_periode[0] - 1, 2, "");
                    $q_prov_rilis_1 =     $this->get_q(0, $arr_periode[0] - 1, 2, 3);
                    $q_prov_revisi_1 =    $this->get_q(0, $arr_periode[0] - 1, 2, "");

                    $kabkot_rilis_1  = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, 3, $q_kabkot_rilis_1);
                    $kabkot_revisi_1 = $this->get_data_cum(1, $arr_periode[0] - 1, $q, 2, "", $q_kabkot_revisi_1);
                    $prov_rilis_1    = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, 3, $q_prov_rilis_1);
                    $prov_revisi_1   = $this->get_data_cum(0, $arr_periode[0] - 1, $q, 2, "", $q_prov_revisi_1);

                    $kabkot_laju_pert_rilis =  $kabkot_rilis && $kabkot_rilis_1 && isset($kabkot_rilis_1->c_pdrb) && $kabkot_rilis_1->c_pdrb != 0 ? ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) / $kabkot_rilis_1->c_pdrb * 100 : null;
                    $kabkot_laju_pert_rev =  $kabkot_revisi && $kabkot_revisi_1 && isset($kabkot_revisi_1->c_pdrb) && $kabkot_revisi_1->c_pdrb != 0 ? ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) / $kabkot_revisi_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rilis =  $prov_rilis && $prov_rilis_1 && isset($prov_rilis_1->c_pdrb) && $prov_rilis_1->c_pdrb != 0 ? ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) / $prov_rilis_1->c_pdrb * 100 : null;
                    $prov_laju_pert_rev =  $prov_revisi && $prov_revisi_1 && isset($prov_revisi_1->c_pdrb) && $prov_revisi_1->c_pdrb != 0 ? ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) / $prov_revisi_1->c_pdrb * 100 : null;

                    $row[$periode . 'kabkot_rilis'] = $kabkot_rilis
                        && $kabkot_rilis_1
                        && $kabkot_laju_pert_rilis
                        && isset($kabkot_rilis->$komp_id)
                        && isset($kabkot_rilis_1->$komp_id)
                        && isset($kabkot_rilis->c_pdrb)
                        && isset($kabkot_rilis_1->c_pdrb)
                        && ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb) != 0                        ?
                        (($kabkot_rilis->$komp_id - $kabkot_rilis_1->$komp_id) / ($kabkot_rilis->c_pdrb - $kabkot_rilis_1->c_pdrb)) * $kabkot_laju_pert_rilis : null;

                    $row[$periode . 'kabkot_revisi'] = $kabkot_revisi
                        && $kabkot_revisi_1
                        && $kabkot_laju_pert_rev
                        && isset($kabkot_revisi->$komp_id)
                        && isset($kabkot_revisi_1->$komp_id)
                        && isset($kabkot_revisi->c_pdrb)
                        && isset($kabkot_revisi_1->c_pdrb)
                        && ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb) != 0                        ?
                        (($kabkot_revisi->$komp_id - $kabkot_revisi_1->$komp_id) / ($kabkot_revisi->c_pdrb - $kabkot_revisi_1->c_pdrb)) * $kabkot_laju_pert_rev : null;

                    $row[$periode . 'prov_rilis'] = $prov_rilis
                        && $prov_rilis_1
                        && $prov_laju_pert_rilis
                        && isset($prov_rilis->$komp_id)
                        && isset($prov_rilis_1->$komp_id)
                        && isset($prov_rilis->c_pdrb)
                        && isset($prov_rilis_1->c_pdrb)
                        && ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb) != 0                        ?
                        (($prov_rilis->$komp_id - $prov_rilis_1->$komp_id) / ($prov_rilis->c_pdrb - $prov_rilis_1->c_pdrb)) * $prov_laju_pert_rilis : null;

                    $row[$periode . 'prov_revisi'] = $prov_revisi
                        && $prov_revisi_1
                        && $prov_laju_pert_rev
                        && isset($prov_revisi->$komp_id)
                        && isset($prov_revisi_1->$komp_id)
                        && isset($prov_revisi->c_pdrb)
                        && isset($prov_revisi_1->c_pdrb)
                        && ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb) != 0                        ?
                        (($prov_revisi->$komp_id - $prov_revisi_1->$komp_id) / ($prov_revisi->c_pdrb - $prov_revisi_1->c_pdrb)) * $prov_laju_pert_rev : null;
                }
                $data[] = $row;
            }
        }
        return $data;
    }

    public function index(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = AssetData::$list_group_12_pkrt; //$this->list_group_komponen;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_12pkrt;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '2.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];

        $default_filter = [];
        foreach ($list_group_komponen as $val) {
            $default_filter[] = $val['column_alias'];
        }
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : $default_filter; //['1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g., 1.h., 1.i., 1.j., 1.k., 1.l.', '2.', '3., 3.a., 3.b.', '4., 4.a., 4.b.', '5.', '6., 6.a., 6.b.', '7., 7.a., 7.b.', '8., 8.a., 8.b.', 'pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : Auth::user()->kdkab;

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);

        return view('revisi.total2', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }

    public function revisi_7pkrt(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen =  AssetData::getGroupKomponen(); //$this->list_group_7pkrt;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_7pkrt;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];

        $default_filter = [];
        foreach ($list_group_komponen as $val) {
            $default_filter[] = $val['column_alias'];
        }
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : $default_filter;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : Auth::user()->kdkab;

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);
        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }

    public function revisi_rilis(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen =  AssetData::$list_group_rilis_komponen; //$this->list_group_rilis;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_rilis;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];

        $default_filter = [];
        foreach ($list_group_komponen as $val) {
            $default_filter[] = $val['column_alias'];
        }
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : $default_filter;
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : Auth::user()->kdkab;

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);
        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }
}
