<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Komponen;

class RevisiTotalController extends Controller
{
    public function index() {
        $periode = DB::select('SELECT DISTINCT CONCAT(tahun, "Q", q) AS periode FROM superi_pdrb');

        return view('revisi.total', compact('periode'));
    }

    public function get(Request $request) {
        $tabel = $request->input('tabel');
        $kd_kab = $request->input('kd_kab');
        $periode = explode(',', $request->input('periode'));

        $c_all = [
            'c_pdrb' => 'PDRB',
            'c_1' => '1. Pengeluaran Konsumsi Rumah Tangga (1.a. s/d 1.l.)',
            'c_1a' => '1.a. Makanan dan Minuman Non Beralkohol',
            'c_1b' => '1.b. Minuman Beralkohol dan Rokok',
            'c_1c' => '1.c. Pakaian',
            'c_1d' => '1.d. Perumahan, Air, Listrik, Gas dan Bahan Bakar Lainnya',
            'c_1e' => '1.e. Perabot, Peralatan rumahtangga dan Pemeliharaan Rutin Rumah',
            'c_1f' => '1.f. Kesehatan',
            'c_1g' => '1.g. Transportasi/Angkutan',
            'c_1h' => '1.h. Komunikasi',
            'c_1i' => '1.i. Rekreasi dan Budaya',
            'c_1j' => '1.j. Pendidikan',
            'c_1k' => '1.k. Penginapan dan Hotel',
            'c_1l' => '1.l. Barang Pribadi dan Jasa Perorangan',
            'c_2' => '2. Pengeluaran Konsumsi LNPRT',
            'c_3' => '3. Pengeluaran Konsumsi Pemerintah (3.a. + 3.b.)',
            'c_3a' => '3.a. Konsumsi Kolektif',
            'c_3b' => '3.b. Konsumsi Individu',
            'c_4' => '4. Pembentukan Modal Tetap Bruto (4.a. + 4.b.)',
            'c_4a' => '4.a. Bangunan',
            'c_4b' => '4.b. Non Bangunan',
            'c_5' => '5. Perubahan Inventori',
            'c_6' => '6. Ekspor Luar Negeri (6.a. + 6.b.)',
            'c_6a' => '6.a. Barang',
            'c_6b' => '6.b. Jasa',
            'c_7' => '7. Impor Luar Negeri (7.a. + 7.b.)',
            'c_7a' => '7.a. Barang',
            'c_7b' => '7.b. Jasa',
            'c_8' => '8. Net Ekspor Antar Daerah (8.a. - 8.b.)',
            'c_8a' => '8.a. Ekspor',
            'c_8b' => '8.b. Impor'
        ];

        $pdrb = [];

        foreach ($c_all as $c => $c_desc) {
            $sql = 'SELECT "' . $c_desc . '" AS `Komponen`, ';

            if ($tabel == '2.1') {
                foreach ($periode as $p) {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.2') {
                foreach ($periode as $p) {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.3') {
                return response()->json([['Data' => 'Tidak Tersedia']]);
            } elseif ($tabel == '2.4') {
                return response()->json([['Data' => 'Tidak Tersedia']]);
            } elseif ($tabel == '2.5') {
                foreach ($periode as $p) {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.6') {
                foreach ($periode as $p) {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.7') {
                foreach ($periode as $p) {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.8') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.9') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    if ($q == '1') {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == '2.10') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.11') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.12') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    if ($q == '1') {
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == '2.13') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before_rev = '(SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.14') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit = '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $implicit_before = '(SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
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
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    } else {
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                    }
                }
            } elseif ($tabel == '2.19') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            } elseif ($tabel == '2.20') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis_' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke = 1 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 1 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi_' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah_' . $p . '`, ';
                }
            }

            $sql = substr($sql, 0, -2);
            $sql .= ' FROM superi_pdrb WHERE CONCAT(kode_prov, kode_kab) = "' . $kd_kab . '"';

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
                        $pdrb_value = 'Naik';
                    } elseif ($pdrb_item->$revisi_key < $pdrb_item->$rilis_key) {
                        $pdrb_value = 'Turun';
                    } else {
                        $pdrb_value = 'Tetap';
                    }
                }
            }
        }

        return response()->json($pdrb);
    }
}
