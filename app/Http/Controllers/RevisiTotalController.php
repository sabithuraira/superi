<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RevisiTotalExportAll;
use App\Exports\RevisiTotalExport;
use App\Helpers\AssetData;

class RevisiTotalController extends Controller
{
    public function index() {
        $tahun = DB::select('SELECT DISTINCT tahun FROM superi_pdrb_rev_view ORDER BY tahun');

        $tahun_now = DB::table('setting')
                     ->select('setting_value')
                     ->where('setting_name', '=', 'tahun_berlaku')
                     ->first();
        $tahun_now = $tahun_now->setting_value;

        $q_now = DB::table('setting')
                 ->select('setting_value')
                 ->where('setting_name', '=', 'triwulan_berlaku')
                 ->first();
        $q_now = $q_now->setting_value;

        $periode = [];
        foreach ($tahun as $t) {
            if ((int) $t->tahun < (int) $tahun_now) {
                $q = 4;
            } else $q = (int) $q_now;

            for ($i = 1; $i <= $q; $i++) {
                array_push($periode, (object) array('periode' => $t->tahun . 'Q' . $i));
            }

            if ($q == 4) array_push($periode, (object) array('periode' => $t->tahun));
        }

        return view('revisi.total', compact('periode'));
    }

    public function get(Request $request) {
        $tabel = $request->input('tabel');
        $kd_kab = $request->input('kd_kab');
        $periode = explode(',', $request->input('periode'));

        $pdrb = $this->total($tabel, $kd_kab, $periode);

        return response()->json($pdrb);
    }

    public function export(Request $request) {
        $judul = $request->input('judul');
        $tabel = $request->input('tabel');
        $kd_kab = $request->input('kd_kab');
        $periode = explode(',', $request->input('periode'));

        $pdrb = $this->total($tabel, $kd_kab, $periode);

        return Excel::download(new RevisiTotalExport($judul, $pdrb, $kd_kab), 'Arah Revisi Total ' . $judul . '.xlsx');
    }

    public function export_all(Request $request) {
        $judul = $request->input('judul');
        $tabel = $request->input('tabel');
        $kd_kab = explode(',', $request->input('kd_kab'));
        $periode = explode(',', $request->input('periode'));

        $pdrb_all = [];
        foreach ($kd_kab as $kd_kab_item) {
            $pdrb = $this->total($tabel, $kd_kab_item, $periode);
            $pdrb_all[$kd_kab_item] = $pdrb;
        }

        return Excel::download(new RevisiTotalExportAll($judul, $pdrb_all), 'All Arah Revisi Total ' . $judul . '.xlsx');
    }

    private function total($tabel, $kd_kab, $periode) {
        $komponen = AssetData::getDetailKomponen();

        $pdrb = [];

        foreach ($komponen as $komponen_item) {
            $c = $komponen_item['select_id'];
            $c_desc = $komponen_item['name'];

            $sql = 'SELECT "' . $c_desc . '" AS `Komponen`, ';

            if ($tabel == 'Tabel 2.1') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.2') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.3') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov .' * 100 AS `Rilis ' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov_rev .' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov .' * 100 AS `Rilis ' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 1, ' . $c . ', 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / ' . $pembagi_prov_rev .' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.4') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.5') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.6') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                    $sql .= 'SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND CONCAT(tahun, "Q", q) = "' . $p . '" AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.7') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= 'SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                    $sql .= 'SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.8') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        } else {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.9') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.10') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Rilis ' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.11') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        } else {
                            $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                            $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.12') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $implicit = '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before = '(SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $implicit_before_rev = '(SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 1, ' . $c . ', 0)) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) * 100)';
                        $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
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
                    $sql .= '(' . $implicit . ' - ' . $implicit_before . ') / ' . $implicit_before . ' * 100 AS `Rilis ' . $p . '`, ';
                    $sql .= '(' . $implicit_rev . ' - ' . $implicit_before_rev . ') / ' . $implicit_before_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.14') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                            $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        } else {
                            $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                            $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.15') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                        $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.16') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $pembagi_prov = DB::select('SELECT SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov . ' * 100 AS `Rilis ' . $p . '`, ';
                    $pembagi_prov_rev = DB::select('SELECT SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND kode_prov = "16" AND kode_kab = "00" AND adhb_or_adhk = 2, c_pdrb, 0)) AS prov FROM superi_pdrb_ori_and_rev_view')[0]->prov;
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / ' . $pembagi_prov_rev . ' * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                }
            } elseif ($tabel == 'Tabel 2.17') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        if ($q == '1') {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = 1 AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = 4 AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        } else {
                            $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                            $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . ($q - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                            $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                        }
                    }
                }
            } elseif ($tabel == 'Tabel 2.18') {
                foreach ($periode as $p) {
                    if (strlen($p) == 4) {
                        $tahun = $p;
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    } else {
                        $tahun = substr($p, 0, 4);
                        $q = substr($p, 5, 1);
                        $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                        $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q = ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                        $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                    }
                }
            } elseif ($tabel == 'Tabel 2.19') {
                foreach ($periode as $p) {
                    $tahun = substr($p, 0, 4);
                    if (strlen($p) == 4) $q = 4;
                    else $q = substr($p, 5, 1);
                    $sql .= '(SUM(IF(revisi_ke = 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke = 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Rilis ' . $p . '`, ';
                    $sql .= '(SUM(IF(revisi_ke > 0 AND tahun = ' . $tahun . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0)) - SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, ' . $c . ', 0))) / SUM(IF(revisi_ke > 0 AND tahun = ' . ($tahun - 1) . ' AND q <= ' . $q . ' AND adhb_or_adhk = 2, c_pdrb, 0)) * 100 AS `Revisi ' . $p . '`, ';
                    $sql .= '"Arah" AS `Arah ' . $p . '`, ';
                }
            }

            $sql = substr($sql, 0, -2);
            $sql .= ' FROM superi_pdrb_ori_and_rev_view WHERE CONCAT(kode_prov, kode_kab) = "' . $kd_kab . '"';

            $pdrb_c = DB::select($sql);

            array_push($pdrb, $pdrb_c[0]);
        }

        foreach ($pdrb as &$pdrb_item) {
            if (preg_match('/^\d+\. /', $pdrb_item->Komponen)) {
                $pdrb_item->Komponen = 'BOLD' . strval($pdrb_item->Komponen);
            }

            foreach ($pdrb_item as $pdrb_key => &$pdrb_value) {
                if (str_contains($pdrb_key, 'Arah')) {
                    $periode_pdrb = explode(' ', $pdrb_key)[1];
                    $rilis_key = 'Rilis ' . $periode_pdrb;
                    $revisi_key = 'Revisi ' . $periode_pdrb;

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

        return $pdrb;
    }
}
