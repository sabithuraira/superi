<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Komponen;

class ResumeController extends Controller
{
    public function index() {
        $komponen = Komponen::all();

        $periode = DB::select('SELECT DISTINCT CONCAT(tahun, "Q", q) AS periode FROM superi_pdrb');

        return view('resume.index', compact('komponen', 'periode'));
    }

    public function get(Request $request) {
        $tabel = $request->input('tabel');
        $c = $request->input('komponen');
        $periode = explode(',', $request->input('periode'));
        $kd_kab = explode(',', $request->input('kd_kab'));

        $sql = 'SELECT CONCAT(kode_prov, kode_kab) AS kd_kab, ';

        if ($tabel == '2.1') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.2') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.3') {
            return response()->json([['Data' => 'Tidak Tersedia']]);
        } elseif ($tabel == '2.4') {
            return response()->json([['Data' => 'Tidak Tersedia']]);
        } elseif ($tabel == '2.5') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.6') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.7') {
            foreach ($periode as $p) {
                $sql .= 'SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.8') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= 'SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.9') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
                } else {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == '2.10') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.11') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.12') {
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
        } elseif ($tabel == '2.13') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $implicit_before = '(SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.14') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $implicit = '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $implicit_before = '(SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.15') {
            return response()->json([['Data' => 'Tidak Tersedia']]);
        } elseif ($tabel == '2.16') {
            return response()->json([['Data' => 'Tidak Tersedia']]);
        } elseif ($tabel == '2.17') {
            return response()->json([['Data' => 'Tidak Tersedia']]);
        } elseif ($tabel == '2.18') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                if ($q == '1') {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
                } else {
                    $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
                }
            }
        } elseif ($tabel == '2.19') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        } elseif ($tabel == '2.20') {
            foreach ($periode as $p) {
                $tahun = substr($p, 0, 4);
                $q = substr($p, 5, 1);
                $sql .= '(SUM(IF(tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `' . $p . '`, ';
            }
        }

        $sql = substr($sql, 0, -2);
        $sql .= ' FROM superi_pdrb';
        $sql .= ' GROUP BY CONCAT(kode_prov, kode_kab)';

        $pdrb = DB::select($sql);

        $pdrb = array_filter($pdrb, function($pdrb_item) use ($kd_kab) {
            if (in_array($pdrb_item->kd_kab, $kd_kab)) return true;
            return false;
        });

        return response()->json($pdrb);
    }
}
