<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Komponen;

class RevisiTotalController extends Controller
{
    public function index() {
        $tahun = DB::select('SELECT DISTINCT tahun FROM superi_pdrb_rev_view ORDER BY tahun');

        $periode = [];
        foreach ($tahun as $t) {
            array_push($periode, (object) array('periode' => $t->tahun . 'Q1'), (object) array('periode' => $t->tahun . 'Q2'), (object) array('periode' => $t->tahun . 'Q3'), (object) array('periode' => $t->tahun . 'Q4'), (object) array('periode' => $t->tahun));
        }

        return view('revisi.total', compact('periode'));
    }

    public function get(Request $request) {
        $tabel = $request->input('tabel');
        $kd_kab = $request->input('kd_kab');
        $periode = explode(',', $request->input('periode'));

        $komponen = DB::table('komponen')
                    ->where('status_aktif', '=', 1)
                    ->orderBy('no_komponen')
                    ->get();

        $c_all = [['no_komponen' => 'c_pdrb', 'nama_komponen' => 'Total PDRB']];

        foreach ($komponen as $k) {
            $c = 'c_' . str_replace('.', '', $k->no_komponen);
            $c_desc = $k->nama_komponen;
            array_push($c_all, ['no_komponen' => $c, 'nama_komponen' => $c_desc]);
        }

        $pdrb = [];

        foreach ($c_all as $c_item) {
            $c = $c_item['no_komponen'];
            $c_desc = $c_item['nama_komponen'];

            $sql = 'SELECT "' . $c_desc . '" AS `Komponen`, ';

            if ($tabel == 'Tabel 2.1') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.2') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.3') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov .' * 100 AS `Rilis_' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov_rev .' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov .' * 100 AS `Rilis_' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov_rev .' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.4') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.5') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.6') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.7') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.8') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        } else {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.9') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.10') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.11') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        } else {
                            $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.12') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.13') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.14') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                            $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        } else {
                            $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                            $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.15') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.16') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis_' . $p . '`, ';
                    $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.17') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) { $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        } else {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.18') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.19') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            }

            $sql = substr($sql, 0, -2);
            $sql .= ' FROM superi_pdrb_ori_and_rev_view WHERE CONCAT(kode_prov, kode_kab) = "' . $kd_kab . '"';

            $pdrb_c = DB::select($sql);

            array_push($pdrb, $pdrb_c[0]);
        }

        foreach ($pdrb as &$pdrb_item) {
            foreach ($pdrb_item as $pdrb_key => &$pdrb_value) {
                if (str_contains($pdrb_key, 'Arah')) {
                    $periode_pdrb = explode('_', $pdrb_key)[1];
                    $rilis_key = 'Rilis_' . $periode_pdrb;
                    $revisi_key = 'Revisi_' . $periode_pdrb;

                    if ($pdrb_item->$revisi_key > $pdrb_item->$rilis_key) {
                        $pdrb_value = 'CENTER<div class="text-success">▲</div>';
                    } elseif ($pdrb_item->$revisi_key < $pdrb_item->$rilis_key) {
                        $pdrb_value = 'CENTER<div class="text-danger">▼</div>';
                    } else {
                        $pdrb_value = 'CENTER<div class="text-warning">═</div>';
                    }

                    if ($pdrb_item->$revisi_key * $pdrb_item->$rilis_key < 0) {
                        $pdrb_item->$revisi_key = 'WARNING' . $pdrb_item->$revisi_key;
                        $pdrb_item->$rilis_key = 'WARNING' . $pdrb_item->$rilis_key;
                    }
                }
            }
        }

        return response()->json($pdrb);
    }
}
