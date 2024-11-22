<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Komponen;

class ResumeController extends Controller
{
    public function index() {
        $komponen = DB::table('komponen')
                    ->where('status_aktif', '=', 1)
                    ->orderBy('no_komponen')
                    ->get();

        $periode = DB::select('SELECT DISTINCT CONCAT(tahun, "Q", q) AS periode FROM superi_pdrb_rev_view');

        return view('resume.index', compact('komponen', 'periode'));
    }

    public function get(Request $request) {
        $tabel = $request->input('tabel');
        $c = $request->input('komponen');
        $periode = explode(',', $request->input('periode'));
        $kd_kab = explode(',', $request->input('kd_kab'));

        $pdrb = [];

        if ($c == 'c_pdrb') {
            $c_desc = 'Total PDRB';
        } else {
            $c_desc = DB::select('SELECT no_komponen, nama_komponen FROM superi_komponen WHERE REPLACE(no_komponen, ".", "") = "' . substr($c, 2) . '"');
            $c_desc = $c_desc[0]->no_komponen . ' ' . $c_desc[0]->nama_komponen;
        }

        $sql = 'SELECT CONCAT(kode_prov, kode_kab) AS kd_kab, "' . $c_desc . '" AS `Komponen`, ';

        if ($tabel == 'Tabel 2.1') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.2') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.3') {
            foreach ($periode as $p) {
                $pembagi_prov = DB::select('SELECT SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_rev_view')[0]->prov;
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov .' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.4') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.5') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.6') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.7') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= 'SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.8') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
                } else {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == 'Tabel 2.9') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.10') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.11') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
                } else {
                    $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == 'Tabel 2.12') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $implicit_before = '(SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.13') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $implicit_before = '(SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.14') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $pembagi_prov = DB::select('SELECT SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `' . $p . '`, ';
                } else {
                    $pembagi_prov = DB::select('SELECT SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == 'Tabel 2.15') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $pembagi_prov = DB::select('SELECT SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_rev_view')[0]->prov;
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.16') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $pembagi_prov = DB::select('SELECT SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_rev_view')[0]->prov;
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.17') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
                } else {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == 'Tabel 2.18') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == 'Tabel 2.19') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        }

        $sql = substr($sql, 0, -2);
        $sql .= ' FROM superi_pdrb_rev_view';
        $sql .= ' GROUP BY CONCAT(kode_prov, kode_kab)';

        $pdrb_c = DB::select($sql);

        foreach ($pdrb_c as $pdrb_item) {
            array_push($pdrb, $pdrb_item);
        }

        $pdrb = array_filter($pdrb, function($pdrb_item) use ($kd_kab) {
            if (in_array($pdrb_item->kd_kab, $kd_kab)) return true;
            return false;
        });

        return response()->json($pdrb);
    }
}
