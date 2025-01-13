<?php

namespace App\Http\Controllers;

use App\Komponen;
use App\SettingApp;
use App\Pdrb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $this->select_12pkrt =  ['kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_7) as c_7, sum(c_pdrb) as c_pdrb')];

        $this->select_7pkrt =  ['kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_7) as c_7, sum(c_pdrb) as c_pdrb')];

        $this->select_rilis = ['kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6 - c_7 + c_5 + c_2) as c_4, sum(c_pdrb) as c_pdrb')];
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
            'name' => 'Tabel 303. Pertumbuhan PDRB (Q-TO-Q)'
        ],
        [
            'id' => '304',
            'name' => 'Tabel 304. Pertumbuhan PDRB (Y-ON-Y)'
        ],
        [
            'id' => '305',
            'name' => 'Tabel 305. Pertumbuhan PDRB (C-TO-C)'
        ],
        [
            'id' => '306',
            'name' => 'Tabel 306. Indeks Implisit'
        ],
        [
            'id' => '307',
            'name' => 'Tabel 307. Pertumbuhan Indeks Implisit (Y-on-Y)'
        ],
        [
            'id' => '308',
            'name' => 'Tabel 308. Sumber Pertumbuhan (Q-to-Q)'
        ],
        [
            'id' => '309',
            'name' => 'Tabel 309. Sumber Pertumbuhan (Y-on-Y)'
        ],
        [
            'id' => '310',
            'name' => 'Tabel 310. Sumber Pertumbuhan (C-to-C)'
        ],
    ];

    public $list_group_komponen = [
        ['column' => "1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g., 1.h., 1.i., 1.j., 1.k., 1.l.", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "2.",                              'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "3., 3.a., 3.b.",                  'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "4., 4.a., 4.b.",                  'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "5.",                              'name' => '5. Perubahan Inventori'],
        ['column' => "6., 6.a., 6.b.",                  'name' => '6. Ekspor Luar Negeri'],
        ['column' => "7., 7.a., 7.b.",                  'name' => '7. Impor Luar Negeri'],
        ['column' => "8., 8.a., 8.b.",                  'name' => '8. Net Ekspor Antar Daerah'],
        ['column' => "pdrb",                            'name' => '9. PDRB'],
    ];
    public $list_group_7pkrt = [
        ['column' => "1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g.", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "2.",                              'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "3., 3.a., 3.b.",                  'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "4., 4.a., 4.b.",                  'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "5.",                              'name' => '5. Perubahan Inventori'],
        ['column' => "6., 6.a., 6.b.",                  'name' => '6. Ekspor Luar Negeri'],
        ['column' => "7., 7.a., 7.b.",                  'name' => '7. Impor Luar Negeri'],
        ['column' => "8., 8.a., 8.b.",                  'name' => '8. Net Ekspor Antar Daerah'],
        ['column' => "pdrb",                            'name' => '9. PDRB'],
    ];
    public $list_group_rilis = [
        ['column' => "1.", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "2.", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "3.", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "4.", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "pdrb", 'name' => '5. PDRB'],
    ];

    public function get_rev($kab, $thn, $q, $adhk, $status)
    {
        $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
            ->where('kode_kab', $kab)
            ->where('tahun', $thn)
            ->where('q', "LIKE", '%' . $q . '%')
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status . '%')
            ->groupBy('kode_kab', 'q')
            ->get();

        return $rev;
    }

    public function get_data($kab, $thn, $q, $adhk, $status, $select)
    {
        $data = Pdrb::select($select)
            ->where('kode_kab', $kab)
            ->where('tahun', $thn)
            ->where('q', "LIKE", '%' . $q . '%')
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status . '%')
            ->orderBy('revisi_ke', 'desc')
            ->groupby('kode_kab')
            ->first();
        return $data;
    }

    public function get_data_cumulative($kab, $thn, $q, $adhk, $status, $rev, $select)
    {
        $data = Pdrb::select($select)
            ->where('kode_kab', $kab)
            ->where('tahun', $thn)
            ->wherein('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->where('status_data', "LIKE", '%' . $status . '%')
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
                    $rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                    $revisi = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, null, $select);
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
                    $rilis = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, null, $select);
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
                    $rilis_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2,  null, $select);
                    if ($arr_periode[1] == 1) {
                        $rilis_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1, $select);
                        $revisi_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2,  null, $select);
                    } else {
                        $rilis_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1, $select);
                        $revisi_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2,  null, $select);
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
                    $rilis_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2,  null, $select);
                    $rilis_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);
                    $revisi_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2,  null, $select);
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
                    $rev_rilis_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                    $rev_revisi_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, null);
                    $rev_rilis_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                    $rev_revisi_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, null);

                    $rilis_c = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, 1, $rev_rilis_y, $select);
                    $revisi_c = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, null, $rev_revisi_y, $select);
                    $rilis_c_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, 1, $rev_rilis_y_1, $select);
                    $revisi_c_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, null, $rev_revisi_y_1, $select);

                    $row[$periode . "_rilis"] = $rilis_c && $rilis_c_1 && isset($rilis_c_1->$komp_id) && $rilis_c_1->$komp_id != 0 ? ($rilis_c->$komp_id - $rilis_c_1->$komp_id) / $rilis_c_1->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_c && $revisi_c_1 && isset($revisi_c_1->$komp_id) && $revisi_c_1->$komp_id != 0 ? ($revisi_c->$komp_id - $revisi_c_1->$komp_id) / $revisi_c_1->$komp_id * 100 : null;
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
                    $rilis_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                    $rilis_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, null, $select);
                    $revisi_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, null, $select);
                    $row[$periode . "_rilis"] = $rilis_hb && $rilis_hk && isset($rilis_hk->$komp_id) && $rilis_hk->$komp_id != 0 ? $rilis_hb->$komp_id / $rilis_hk->$komp_id * 100 : null;
                    $row[$periode . "_revisi"] = $revisi_hb && $revisi_hk && isset($revisi_hk->$komp_id) && $revisi_hk->$komp_id != 0 ? $revisi_hb->$komp_id / $revisi_hk->$komp_id * 100 : null;
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
                    $rilis_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                    $rilis_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $rilis_hb_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 1, 1, $select);
                    $rilis_hk_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);

                    $revisi_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, null, $select);
                    $revisi_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, null, $select);
                    $revisi_hb_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 1, null, $select);
                    $revisi_hk_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, null, $select);

                    $implisit_rilis = $rilis_hb && $rilis_hk && isset($rilis_hk->$komp_id) && $rilis_hk->$komp_id != 0 ? $rilis_hb->$komp_id / $rilis_hk->$komp_id * 100 : null;
                    $implisit_rilis_1 = $rilis_hb_1 && $rilis_hk_1 && isset($rilis_hk_1->$komp_id) && $rilis_hk_1->$komp_id != 0 ? $rilis_hb_1->$komp_id / $rilis_hk_1->$komp_id * 100 : null;
                    $implisit_revisi = $revisi_hb && $revisi_hk && isset($revisi_hk->$komp_id) && $revisi_hk->$komp_id != 0 ? $revisi_hb->$komp_id / $revisi_hk->$komp_id * 100 : null;
                    $implisit_revisi_1 = $revisi_hb_1 && $revisi_hk_1 && isset($revisi_hk_1->$komp_id) && $revisi_hk_1->$komp_id != 0 ? $revisi_hb_1->$komp_id / $revisi_hk_1->$komp_id * 100 : null;

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
                    $rilis_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2,  null, $select);

                    if ($arr_periode[1] == 1) {
                        $rilis_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1, $select);
                        $revisi_q_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2,  null, $select);
                    } else {
                        $rilis_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1, $select);
                        $revisi_q_1 = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2,  null, $select);
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
                    $rilis_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    $revisi_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2,  null, $select);
                    $rilis_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);
                    $revisi_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2,  null, $select);
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

                    $rev_rilis_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                    $rev_revisi_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, null);
                    $rev_rilis_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                    $rev_revisi_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, null);

                    $rilis_c = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, 1, $rev_rilis_y, $select);
                    $revisi_c = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, null, $rev_revisi_y, $select);
                    $rilis_c_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, 1, $rev_rilis_y_1, $select);
                    $revisi_c_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, null, $rev_revisi_y_1, $select);

                    $laju_pert_rilis =  $rilis_c && $rilis_c_1 && isset($rilis_c_1->c_pdrb) && $rilis_c_1->c_pdrb != 0 ? ($rilis_c->c_pdrb - $rilis_c_1->c_pdrb) / $rilis_c_1->c_pdrb * 100 : null;
                    $laju_pert_rev =  $revisi_c && $revisi_c_1 && isset($revisi_c_1->c_pdrb) && $revisi_c_1->c_pdrb != 0 ? ($revisi_c->c_pdrb - $revisi_c_1->c_pdrb) / $revisi_c_1->c_pdrb * 100 : null;
                    $row[$periode . '_rilis'] = $rilis_c
                        && $rilis_c_1
                        && $laju_pert_rilis
                        && isset($rilis_c->$komp_id)
                        && isset($rilis_c_1->$komp_id)
                        && isset($rilis_c->c_pdrb)
                        && isset($rilis_c_1->c_pdrb)
                        && ($rilis_c->c_pdrb - $rilis_c_1->c_pdrb) != 0                        ?
                        (($rilis_c->$komp_id - $rilis_c_1->$komp_id) / ($rilis_c->c_pdrb - $rilis_c_1->c_pdrb)) * $laju_pert_rilis : null;
                    $row[$periode . '_revisi'] = $revisi_c
                        && $revisi_c_1
                        && $laju_pert_rev
                        && isset($revisi_c->$komp_id)
                        && isset($revisi_c_1->$komp_id)
                        && isset($revisi_c->c_pdrb)
                        && isset($revisi_c_1->c_pdrb)
                        && ($revisi_c->c_pdrb - $revisi_c_1->c_pdrb) != 0                        ?
                        (($revisi_c->$komp_id - $revisi_c_1->$komp_id) / ($revisi_c->c_pdrb - $revisi_c_1->c_pdrb)) * $laju_pert_rev : null;
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
        $list_group_komponen = $this->list_group_komponen;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_12pkrt;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g., 1.h., 1.i., 1.j., 1.k., 1.l.', '2.', '3., 3.a., 3.b.', '4., 4.a., 4.b.', '5.', '6., 6.a., 6.b.', '7., 7.a., 7.b.', '8., 8.a., 8.b.', 'pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);

        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }
    public function revisi_7pkrt(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_7pkrt;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_7pkrt;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['1., 1.a., 1.b., 1.c., 1.d., 1.e., 1.f., 1.g.', '2.', '3., 3.a., 3.b.', '4., 4.a., 4.b.', '5.', '6., 6.a., 6.b.', '7., 7.a., 7.b.', '8., 8.a., 8.b.', 'pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';


        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);
        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }

    public function revisi_rilis(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_rilis;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_rilis;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '301';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4'];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['1.', '2.', '3.', '4.', 'pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponen_filter, $select);
        return view('revisi.kabkot', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'wilayah_filter', 'komponen_filter', 'data'));
    }
}
