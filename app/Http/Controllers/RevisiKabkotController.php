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

class RevisiKabkotController extends Controller
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

        for ($i = 1; $i <= $this->triwulan_berlaku; $i++) {
            array_push($this->list_periode, "{$this->tahun_berlaku}Q{$i}");
        }

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
            $str_sql_select12 .=  "SUM(" . $item['id'] . ") as " . $item['id'] . ",";
        }

        $this->select_12pkrt =  ['kode_kab', DB::raw(substr($str_sql_select12, 0, -1))];

        ///////////////////
        $str_sql_select_rilis = "";
        $list_komp_rilis = AssetData::$list_detail_komponen_rilis;
        foreach ($list_komp_rilis as $item) {
            $str_sql_select_rilis .=  "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }

        $this->select_rilis =  ['kode_kab', DB::raw(substr($str_sql_select_rilis, 0, -1))];
    }

    public $list_tabel = [
        [
            'id' => '301',
            'name' => 'Tabel 301. PDRB ADHB Menurut Pengeluaran, (Juta Rp)'
        ],
        [
            'id' => '302',
            'name' => 'Tabel 302. PDRB ADHK Menurut Pengeluaran, (Juta Rp)'
        ],
        [
            'id' => '303',
            'name' => 'Tabel 303. Pertumbuhan PDRB (Q-TO-Q), (persen)'
        ],
        [
            'id' => '304',
            'name' => 'Tabel 304. Pertumbuhan PDRB (Y-ON-Y), (persen)'
        ],
        [
            'id' => '305',
            'name' => 'Tabel 305. Pertumbuhan PDRB (C-TO-C), (persen)'
        ],
        [
            'id' => '306',
            'name' => 'Tabel 306. Indeks Implisit'
        ],
        [
            'id' => '307',
            'name' => 'Tabel 307. Pertumbuhan Indeks Implisit (Y-on-Y), (persen)'
        ],
        [
            'id' => '308',
            'name' => 'Tabel 308. Sumber Pertumbuhan (Q-to-Q), (persen)'
        ],
        [
            'id' => '309',
            'name' => 'Tabel 309. Sumber Pertumbuhan (Y-on-Y), (persen)'
        ],
        [
            'id' => '310',
            'name' => 'Tabel 310. Sumber Pertumbuhan (C-to-C), (persen)'
        ],
    ];

    // get data last revisi each q/tw
    public function get_q($kab, $thn, $adhk, $status_rilis)
    {
        $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
            ->where('kode_kab', '=', $kab)
            ->where('tahun', $thn)
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status_rilis . '%')
            ->groupBy('kode_kab', 'q')
            ->get();
        return $rev;
    }

    // get data using parameters
    public function get_data_cum($kab, $thn, $q, $adhk, $status_rilis, $rev, $select)
    {
        $data = Pdrb::select($select)
            ->where('kode_kab', '=', $kab)
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
            ->groupBy('kode_kab')
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
        $list_detail_komponen = Komponen::wherein('no_komponen', $array_komp_filter)->orderby('no_komponen')->get()->toArray();
        array_push($list_detail_komponen, ['id' => '26', 'no_komponen' => 'pdrb', 'nama_komponen' => "PDRB"]);
        if ($id === '301') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis = $this->get_q($wilayah_filter, $arr_periode[0], 1, 3);
                    $q_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 1, "");
                    $rilis = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_rilis, $select);
                    $revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, "", $q_revisi, $select);

                    $row[$periode . "_rilis"] = $rilis ? $rilis->$komp_id : null;
                    $row[$periode . "_revisi"] = $revisi ? $revisi->$komp_id : null;
                }
                $data[] = $row;
            }
        } else if ($id === '302') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);
                    $row[$periode . "_rilis"] = $rilis ? $rilis->$komp_id : null;
                    $row[$periode . "_revisi"] = $revisi ? $revisi->$komp_id : null;
                }
                $data[] = $row;
            }
        } else if ($id === '303') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    if ($arr_periode[1] == 1) {
                        $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                        $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                        $rilis_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 2, 3, $q_rilis_1, $select);
                        $revisi_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 2, "", $q_revisi_1, $select);
                    } else {
                        $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                        $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                        $rilis_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_rilis_1, $select);
                        $revisi_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_revisi_1, $select);
                    }
                    $row[$periode . "_rilis"] = $rilis_y && $rilis_q_1 && isset($rilis_q_1->$komp_id) && $rilis_q_1->$komp_id != 0 ? ($rilis_y->$komp_id - $rilis_q_1->$komp_id) / $rilis_q_1->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_y && $revisi_q_1 && isset($revisi_q_1->$komp_id) && $revisi_q_1->$komp_id != 0 ? ($revisi_y->$komp_id - $revisi_q_1->$komp_id) / $revisi_q_1->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '304') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                    $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                    $rilis_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_rilis_1, $select);
                    $revisi_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_revisi_1, $select);

                    $row[$periode . "_rilis"] = $rilis_y && $rilis_y_1 && isset($revisi_y_1->$komp_id) && $rilis_y_1->$komp_id != 0 ? ($rilis_y->$komp_id - $rilis_y_1->$komp_id) / $rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_y && $revisi_y_1 && isset($revisi_y_1->$komp_id) && $revisi_y_1->$komp_id != 0 ? ($revisi_y->$komp_id - $revisi_y_1->$komp_id) / $revisi_y_1->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '305') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
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

                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y    = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 2, 3, $q_rilis, $select);
                    $revisi_y   = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 2, "", $q_revisi, $select);

                    $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                    $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                    $rilis_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, $q, 2, 3, $q_rilis_1, $select);
                    $revisi_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, $q, 2, "", $q_revisi_1, $select);

                    $row[$periode . "_rilis"] = $rilis_y && $rilis_y_1 && isset($revisi_y_1->$komp_id) && $rilis_y_1->$komp_id != 0 ? ($rilis_y->$komp_id - $rilis_y_1->$komp_id) / $rilis_y_1->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_y && $revisi_y_1 && isset($revisi_y_1->$komp_id) && $revisi_y_1->$komp_id != 0 ? ($revisi_y->$komp_id - $revisi_y_1->$komp_id) / $revisi_y_1->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '306') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y_hb    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_rilis, $select);
                    $rilis_y_hk    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y_hb   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, "", $q_revisi, $select);
                    $revisi_y_hk   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    $row[$periode . "_rilis"] = $rilis_y_hb && $rilis_y_hk && isset($rilis_y_hk->$komp_id) && $rilis_y_hk->$komp_id != 0 ? $rilis_y_hb->$komp_id / $rilis_y_hk->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_y_hb && $revisi_y_hk && isset($revisi_y_hk->$komp_id) && $revisi_y_hk->$komp_id != 0 ? $revisi_y_hb->$komp_id / $revisi_y_hk->$komp_id * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '307') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y_hb    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_rilis, $select);
                    $rilis_y_hk    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y_hb   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, "", $q_revisi, $select);
                    $revisi_y_hk   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    $q_rilis_1    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi_1   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y_hb_1    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, 3, $q_rilis_1, $select);
                    $rilis_y_hk_1    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis_1, $select);
                    $revisi_y_hb_1   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 1, "", $q_revisi_1, $select);
                    $revisi_y_hk_1   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi_1, $select);

                    $implisit_rilis = $rilis_y_hb && $rilis_y_hk && isset($rilis_y_hk->$komp_id) && $rilis_y_hk->$komp_id != 0 ? $rilis_y_hb->$komp_id / $rilis_y_hk->$komp_id * 100 : null;
                    $implisit_revisi = $revisi_y_hb && $revisi_y_hk && isset($revisi_y_hk->$komp_id) && $revisi_y_hk->$komp_id != 0 ? $revisi_y_hb->$komp_id / $revisi_y_hk->$komp_id * 100 : null;
                    $implisit_rilis_1 = $rilis_y_hb_1 && $rilis_y_hk_1 && isset($rilis_y_hk_1->$komp_id) && $rilis_y_hk_1->$komp_id != 0 ? $rilis_y_hb_1->$komp_id / $rilis_y_hk_1->$komp_id * 100 : null;
                    $implisit_revisi_1 = $revisi_y_hb_1 && $revisi_y_hk_1 && isset($revisi_y_hk_1->$komp_id) && $revisi_y_hk_1->$komp_id != 0 ? $revisi_y_hb_1->$komp_id / $revisi_y_hk_1->$komp_id * 100 : null;

                    $row[$periode . "_rilis"] = $implisit_rilis && $implisit_rilis_1 && $implisit_rilis_1 != 0 ? ($implisit_rilis - $implisit_rilis_1) / $implisit_rilis_1 * 100 : null;
                    $row[$periode . "_revisi"] = $implisit_revisi && $implisit_revisi_1 && $implisit_revisi_1 != 0 ? ($implisit_revisi - $implisit_revisi_1) / $implisit_revisi_1 * 100 : null;;
                }
                $data[] = $row;
            }
        } elseif ($id === '308') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    $q_rilis = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    if ($arr_periode[1] == 1) {
                        $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                        $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                        $rilis_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 2, 3, $q_rilis_1, $select);
                        $revisi_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [4], 2, "", $q_revisi_1, $select);
                    } else {
                        $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                        $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                        $rilis_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 2, 3, $q_rilis_1, $select);
                        $revisi_q_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1] - 1], 2, "", $q_revisi_1, $select);
                    }
                    $laju_pert_rilis =  $rilis_y && $rilis_q_1 && isset($rilis_q_1->c_pdrb) && $rilis_q_1->c_pdrb != 0 ? ($rilis_y->c_pdrb - $rilis_q_1->c_pdrb) / $rilis_q_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $revisi_y && $revisi_q_1 && isset($revisi_q_1->c_pdrb) && $revisi_q_1->c_pdrb != 0 ? ($revisi_y->c_pdrb - $revisi_q_1->c_pdrb) / $revisi_q_1->c_pdrb * 100 : null;

                    $row[$periode . '_rilis'] = $rilis_y
                        && $rilis_q_1
                        && $laju_pert_rilis
                        && isset($rilis_y->$komp_id)
                        && isset($rilis_q_1->$komp_id)
                        && isset($rilis_y->c_pdrb)
                        && isset($rilis_q_1->c_pdrb)
                        && ($rilis_y->c_pdrb - $rilis_q_1->c_pdrb) != 0                        ?
                        (($rilis_y->$komp_id - $rilis_q_1->$komp_id) / ($rilis_y->c_pdrb - $rilis_q_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . '_revisi'] = $revisi_y
                        && $revisi_q_1
                        && $laju_pert_rev
                        && isset($revisi_y->$komp_id)
                        && isset($revisi_q_1->$komp_id)
                        && isset($revisi_y->c_pdrb)
                        && isset($revisi_q_1->c_pdrb)
                        && ($revisi_y->c_pdrb - $revisi_q_1->c_pdrb) != 0                        ?
                        (($revisi_y->$komp_id - $revisi_q_1->$komp_id) / ($revisi_y->c_pdrb - $revisi_q_1->c_pdrb)) * $laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '309') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y    = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, 3, $q_rilis, $select);
                    $revisi_y   = $this->get_data_cum($wilayah_filter, $arr_periode[0], [$arr_periode[1]], 2, "", $q_revisi, $select);

                    $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                    $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                    $rilis_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 2, 3, $q_rilis_1, $select);
                    $revisi_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, [$arr_periode[1]], 2, "", $q_revisi_1, $select);

                    $laju_pert_rilis =  $rilis_y && $rilis_y_1 && isset($rilis_y_1->c_pdrb) && $rilis_y_1->c_pdrb != 0 ? ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb) / $rilis_y_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $revisi_y && $revisi_y_1 && isset($revisi_y_1->c_pdrb) && $revisi_y_1->c_pdrb != 0 ? ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb) / $revisi_y_1->c_pdrb * 100 : null;
                    $row[$periode . '_rilis'] = $rilis_y
                        && $rilis_y_1
                        && $laju_pert_rilis
                        && isset($rilis_y->$komp_id)
                        && isset($rilis_y_1->$komp_id)
                        && isset($rilis_y->c_pdrb)
                        && isset($rilis_y_1->c_pdrb)
                        && ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb) != 0                        ?
                        (($rilis_y->$komp_id - $rilis_y_1->$komp_id) / ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;

                    $row[$periode . '_revisi'] = $revisi_y
                        && $revisi_y_1
                        && $laju_pert_rev
                        && isset($revisi_y->$komp_id)
                        && isset($revisi_y_1->$komp_id)
                        && isset($revisi_y->c_pdrb)
                        && isset($revisi_y_1->c_pdrb)
                        && ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb) != 0                        ?
                        (($revisi_y->$komp_id - $revisi_y_1->$komp_id) / ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb)) * $laju_pert_rev : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '310') {
            foreach ($list_detail_komponen as $komponen) {
                $row = [];
                $row = [
                    'id' => $komponen['no_komponen'],
                    'name' => $komponen['nama_komponen'],
                ];
                $komp_id = 'c_' . str_replace(".", "", $komponen['no_komponen']);
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

                    $q_rilis    = $this->get_q($wilayah_filter, $arr_periode[0], 2, 3);
                    $q_revisi   = $this->get_q($wilayah_filter, $arr_periode[0], 2, "");
                    $rilis_y    = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 2, 3, $q_rilis, $select);
                    $revisi_y   = $this->get_data_cum($wilayah_filter, $arr_periode[0], $q, 2, "", $q_revisi, $select);

                    $q_rilis_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, 3);
                    $q_revisi_1 = $this->get_q($wilayah_filter, $arr_periode[0] - 1, 2, "");
                    $rilis_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, $q, 2, 3, $q_rilis_1, $select);
                    $revisi_y_1 = $this->get_data_cum($wilayah_filter, $arr_periode[0] - 1, $q, 2, "", $q_revisi_1, $select);

                    $laju_pert_rilis =  $rilis_y && $rilis_y_1 && isset($rilis_y_1->c_pdrb) && $rilis_y_1->c_pdrb != 0 ? ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb) / $rilis_y_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $revisi_y && $revisi_y_1 && isset($revisi_y_1->c_pdrb) && $revisi_y_1->c_pdrb != 0 ? ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb) / $revisi_y_1->c_pdrb * 100 : null;
                    $row[$periode . '_rilis'] = $rilis_y
                        && $rilis_y_1
                        && $laju_pert_rilis
                        && isset($rilis_y->$komp_id)
                        && isset($rilis_y_1->$komp_id)
                        && isset($rilis_y->c_pdrb)
                        && isset($rilis_y_1->c_pdrb)
                        && ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb) != 0                        ?
                        (($rilis_y->$komp_id - $rilis_y_1->$komp_id) / ($rilis_y->c_pdrb - $rilis_y_1->c_pdrb)) * $laju_pert_rilis : null;
                    $row[$periode . '_revisi'] = $revisi_y
                        && $revisi_y_1
                        && $laju_pert_rev
                        && isset($revisi_y->$komp_id)
                        && isset($revisi_y_1->$komp_id)
                        && isset($revisi_y->c_pdrb)
                        && isset($revisi_y_1->c_pdrb)
                        && ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb) != 0                        ?
                        (($revisi_y->$komp_id - $revisi_y_1->$komp_id) / ($revisi_y->c_pdrb - $revisi_y_1->c_pdrb)) * $laju_pert_rev : null;
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

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];

        $default_filter = [];
        foreach ($list_group_komponen as $val) {
            $default_filter[] = $val['column_alias'];
        }
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : $default_filter; //['1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g., 1.h., 1.i., 1.j., 1.k., 1.l.', '2.', '3., 3.a., 3.b.', '4., 4.a., 4.b.', '5.', '6., 6.a., 6.b.', '7., 7.a., 7.b.', '8., 8.a., 8.b.', 'pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : Auth::user()->kdkab;

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);

        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
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
