<?php

namespace App\Http\Controllers;

use App\Helpers\AssetData;
use App\Pdrb;
use App\PdrbFinal;
use App\SettingApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResumeController2 extends Controller
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

        for ($i = $this->tahun_berlaku - 3; $i <= $this->tahun_berlaku; $i++) {
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

    public function index(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = AssetData::getDetailKomponen();
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '2.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : 'c_pdrb';

        $data = $this->rumus($id, $list_wilayah, $periode_filter, $komponen_filter);
        return view('resume.index2', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_wilayah', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'komponen_filter', 'data'));
    }

    public function get_data($kd_kab, $thn, $q, $adhk)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);
        $data = PdrbFinal::select('kode_kab', DB::raw($str_sql_select))
            ->where('kode_kab', $kd_kab)
            ->where('tahun', $thn)
            ->whereIn('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->groupBy('kode_kab')
            ->first();
        return $data;
    }

    public function get_data_dikre($kd_kab, $thn, $q, $adhk)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);
        $data = PdrbFinal::select('kode_prov', DB::raw($str_sql_select))
            ->where('kode_kab', '!=', $kd_kab)
            ->where('tahun', $thn)
            ->whereIn('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->groupBy('kode_prov')
            ->first();
        return $data;
    }

    public function rumus($id, $list_wilayah, $periode_filter, $komponen_filter)
    {
        $data = [];
        if ($id === '2.1') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $row[$periode] = isset($d_y0->$komponen_filter) ? $d_y0->$komponen_filter : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.2') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) ? $d_y0->$komponen_filter : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.3') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $dp_y0 = $this->get_data('00', $arr_periode[0], $q, 2);
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($dp_y0->$komponen_filter) ? $d_y0->$komponen_filter / $dp_y0->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.4') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y0->c_pdrb) ? $d_y0->$komponen_filter / $d_y0->c_pdrb * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.5') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y0->c_pdrb) ? $d_y0->$komponen_filter / $d_y0->c_pdrb * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.6') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_hb_y = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $d_hk_y = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $row[$periode] = isset($d_hb_y->$komponen_filter) && isset($d_hk_y->c_pdrb) ? $d_hb_y->$komponen_filter / $d_hk_y->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.7') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_hb_y = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $d_hk_y = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $row[$periode] = isset($d_hb_y->$komponen_filter) && isset($d_hk_y->c_pdrb) ? $d_hb_y->$komponen_filter / $d_hk_y->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.8') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                        if ($arr_periode[1] == '1') {
                            $y_1 = $arr_periode[0] - 1;
                            $q_1 = [4];
                        } else {
                            $y_1 = $arr_periode[0];
                            $q_1 = [$arr_periode[1] - 1];
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                        $y_1 = $arr_periode[0] - 1;
                        $q_1 = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_q1 = $this->get_data($wil_id, $y_1, $q_1, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_q1->$komponen_filter) ? ($d_y0->$komponen_filter - $d_q1->$komponen_filter) / $d_q1->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.9') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / $d_y1->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.10') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);
                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / $d_y1->$komponen_filter * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.11') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                        if ($arr_periode[1] == '1') {
                            $y_1 = $arr_periode[0] - 1;
                            $q_1 = [4];
                        } else {
                            $y_1 = $arr_periode[0];
                            $q_1 = [$arr_periode[1] - 1];
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                        $y_1 = $arr_periode[0] - 1;
                        $q_1 = [1, 2, 3, 4];
                    }

                    $d_hb_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $d_hk_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_hb_y1 = $this->get_data($wil_id, $y_1, $q_1, 1);
                    $d_hk_y1 = $this->get_data($wil_id, $y_1, $q_1, 2);

                    $imp_y0 = isset($d_hb_y0->$komponen_filter) && isset($d_hk_y0->$komponen_filter) && $d_hk_y0->$komponen_filter != 0 ? $d_hb_y0->$komponen_filter / $d_hk_y0->$komponen_filter * 100 : null;
                    $imp_y1 = isset($d_hb_y1->$komponen_filter) && isset($d_hk_y1->$komponen_filter)  && $d_hk_y1->$komponen_filter != 0 ? $d_hb_y1->$komponen_filter / $d_hk_y1->$komponen_filter * 100 : null;
                    $row[$periode] = isset($imp_y0) && isset($imp_y1) && $imp_y1 != 0  ? ($imp_y0 - $imp_y1) / $imp_y1 * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.12') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $d_hb_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $d_hk_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_hb_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 1);
                    $d_hk_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $imp_y0 = isset($d_hb_y0->$komponen_filter) && isset($d_hk_y0->$komponen_filter) && $d_hk_y0->$komponen_filter != 0 ? $d_hb_y0->$komponen_filter / $d_hk_y0->$komponen_filter * 100 : null;
                    $imp_y1 = isset($d_hb_y1->$komponen_filter) && isset($d_hk_y1->$komponen_filter)  && $d_hk_y1->$komponen_filter != 0 ? $d_hb_y1->$komponen_filter / $d_hk_y1->$komponen_filter * 100 : null;
                    $row[$periode] = isset($imp_y0) && isset($imp_y1) && $imp_y1 != 0  ? ($imp_y0 - $imp_y1) / $imp_y1 * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.13') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $d_hb_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 1);
                    $d_hk_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_hb_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 1);
                    $d_hk_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $imp_y0 = isset($d_hb_y0->$komponen_filter) && isset($d_hk_y0->$komponen_filter) && $d_hk_y0->$komponen_filter != 0 ? $d_hb_y0->$komponen_filter / $d_hk_y0->$komponen_filter * 100 : null;
                    $imp_y1 = isset($d_hb_y1->$komponen_filter) && isset($d_hk_y1->$komponen_filter)  && $d_hk_y1->$komponen_filter != 0 ? $d_hb_y1->$komponen_filter / $d_hk_y1->$komponen_filter * 100 : null;
                    $row[$periode] = isset($imp_y0) && isset($imp_y1) && $imp_y1 != 0  ? ($imp_y0 - $imp_y1) / $imp_y1 * 100 : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.14') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                        if ($arr_periode[1] == '1') {
                            $y_1 = $arr_periode[0] - 1;
                            $q_1 = [4];
                        } else {
                            $y_1 = $arr_periode[0];
                            $q_1 = [$arr_periode[1] - 1];
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                        $y_1 = $arr_periode[0] - 1;
                        $q_1 = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $y_1, $q_1, 2);

                    $dk_y0 = $this->get_data_dikre($wil_id, $arr_periode[0], $q, 2);
                    $dk_y1 = $this->get_data_dikre($wil_id, $y_1, $q_1, 2);

                    $laju_pertumbuhan  = isset($dk_y0->c_pdrb) && isset($dk_y1->c_pdrb) && $dk_y1->c_pdrb != 0 ? ($dk_y0->c_pdrb - $dk_y1->c_pdrb) / $dk_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($dk_y0->$komponen_filter - $dk_y1->$komponen_filter) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.15') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $dk_y0 = $this->get_data_dikre($wil_id, $arr_periode[0], $q, 2);
                    $dk_y1 = $this->get_data_dikre($wil_id, $arr_periode[0] - 1, $q, 2);

                    $laju_pertumbuhan  = isset($dk_y0->c_pdrb) && isset($dk_y1->c_pdrb) && $dk_y1->c_pdrb != 0 ? ($dk_y0->c_pdrb - $dk_y1->c_pdrb) / $dk_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($dk_y0->$komponen_filter - $dk_y1->$komponen_filter) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.16') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $dk_y0 = $this->get_data_dikre($wil_id, $arr_periode[0], $q, 2);
                    $dk_y1 = $this->get_data_dikre($wil_id, $arr_periode[0] - 1, $q, 2);

                    $laju_pertumbuhan  = isset($dk_y0->c_pdrb) && isset($dk_y1->c_pdrb) && $dk_y1->c_pdrb != 0 ? ($dk_y0->c_pdrb - $dk_y1->c_pdrb) / $dk_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($dk_y0->$komponen_filter - $dk_y1->$komponen_filter) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.17') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                        if ($arr_periode[1] == '1') {
                            $y_1 = $arr_periode[0] - 1;
                            $q_1 = [4];
                        } else {
                            $y_1 = $arr_periode[0];
                            $q_1 = [$arr_periode[1] - 1];
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                        $y_1 = $arr_periode[0] - 1;
                        $q_1 = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $y_1, $q_1, 2);

                    $laju_pertumbuhan  = isset($d_y0->c_pdrb) && isset($d_y1->c_pdrb) && $d_y1->c_pdrb != 0 ? ($d_y0->c_pdrb - $d_y1->c_pdrb) / $d_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($d_y0->c_pdrb - $d_y1->c_pdrb) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.18') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);

                    if (sizeof($arr_periode) > 1) {
                        $q = [$arr_periode[1]];
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $laju_pertumbuhan  = isset($d_y0->c_pdrb) && isset($d_y1->c_pdrb) && $d_y1->c_pdrb != 0 ? ($d_y0->c_pdrb - $d_y1->c_pdrb) / $d_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($d_y0->c_pdrb - $d_y1->c_pdrb) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        } elseif ($id === '2.19') {
            $row = [];
            foreach ($list_wilayah as $wil_id => $wilayah) {
                $row['id'] = $wil_id;
                $row['name'] = $wilayah;
                foreach ($periode_filter as $periode) {
                    $arr_periode = explode("Q", $periode);
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $d_y0 = $this->get_data($wil_id, $arr_periode[0], $q, 2);
                    $d_y1 = $this->get_data($wil_id, $arr_periode[0] - 1, $q, 2);

                    $laju_pertumbuhan  = isset($d_y0->c_pdrb) && isset($d_y1->c_pdrb) && $d_y1->c_pdrb != 0 ? ($d_y0->c_pdrb - $d_y1->c_pdrb) / $d_y1->c_pdrb * 100 : null;

                    $row[$periode] = isset($d_y0->$komponen_filter) && isset($d_y1->$komponen_filter) && $laju_pertumbuhan
                        ? ($d_y0->$komponen_filter - $d_y1->$komponen_filter) / ($d_y0->c_pdrb - $d_y1->c_pdrb) * $laju_pertumbuhan : null;
                }
                $data[] = $row;
            }
        }
        return $data;
    }
}
