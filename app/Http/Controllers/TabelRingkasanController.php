<?php

namespace App\Http\Controllers;

use App\Pdrb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Cumulative;
use SebastianBergmann\CodeUnitReverseLookup\Wizard;

class TabelRingkasanController extends Controller
{
    public $list_tabel = [
        [
            'id' => '1.1',
            'name' => 'Tabel 1.1. Pertumbuhan Ekonomi Provinsi Menurut Komponen',
            'url' => 'pdrb_ringkasan1'
        ],
        [
            'id' => '1.2',
            'name' => 'Tabel 1.2.  Pertumbuhan Implisit Provinsi Menurut Komponen',
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
            'name' => 'Tabel 1.7. Distribusi Konstan Per Komponen/Sub Komponen Kabupaten Kota',
            'url' => 'pdrb_ringkasan3'
        ],
        [
            'id' => '1.8',
            'name' => 'Tabel 1.8. Distribusi Berlaku Per Komponen/Sub Komponen Kabupaten Kota',
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
            'name' => 'Tabel 1.11. Perbandingan Diskrepansi Nasional dan Regional Menurut Komponen',
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
    public $list_periode = [
        '2024Q1',
        '2024Q2',
        '2024Q3',
        '2024Q4',
        '2024'
    ];

    public $list_group_komponen = [
        ['column' => "c_pdrb", 'name' => 'PDRB'],
        ['column' => "c_1, c_1a, c_1b, c_1d, c_1e, c_1f, c_1g", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3, c_3a, c_3b", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4, c_4a, c_4b", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_5", 'name' => '5. Perubahan Inventori'],
        ['column' => "c_6, c_6a, c_6b", 'name' => '6. Ekspor Luar Negeri'],
        ['column' => "c_7, c_7a, c_7b", 'name' => '7. Impor Luar Negeri']
    ];

    public $list_detail_komponen = [
        ['id' => 'c_pdrb', 'alias' => 'PDRB',               'name' =>  'PDRB'],
        ['id' => 'c_1',   'alias' => '1. PKRT',             'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['id' => 'c_1a',  'alias' => '1a. PKRT-Mamin  ',    'name' =>  '1.a. Makanan dan Minuman, Selain Restoran'],
        ['id' => 'c_1b',  'alias' => '1b. PKRT-Pakaian',    'name' =>  '1.b. Pakaian, Alas Kaki dan Jasa Perawatannya'],
        ['id' => 'c_1c',  'alias' => '1c. PKRT-Perumahan',  'name' =>  '1.c. Perumahan dan Perlengkapan Rumahtangga'],
        ['id' => 'c_1d',  'alias' => '1d. PKRT-Kesehatan ', 'name' =>  '1.d. Kesehatan dan Pendidikan'],
        ['id' => 'c_1e',  'alias' => '1e. PKRT-Tansport',   'name' =>  '1.e. Transportasi dan Komunikasi'],
        ['id' => 'c_1f',  'alias' => '1f. PKRT-Restoran ',  'name' =>  '1.f. Restoran dan Hotel'],
        ['id' => 'c_1g',  'alias' => '1g. PKRT-Lainnya',    'name' =>  '1.g. Lainnya'],
        ['id' => 'c_2',   'alias' => '2. PKLNPRT',          'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['id' => 'c_3',   'alias' => '3.PKP',               'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['id' => 'c_3a',  'alias' => '3a.PKP-Kol',          'name' =>  '  3.a. Konsumsi Kolektif'],
        ['id' => 'c_3b',  'alias' => '3b.PKP-Ind',          'name' =>  '  3.b. Konsumsi Individu'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_4a',  'alias' => '4a. PMTB-Bang',       'name' =>  '  4.a. Bangunan'],
        ['id' => 'c_4b',  'alias' => '4b. PMTB-NB',         'name' =>  '  4.b. Non Bangunan'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        ['id' => 'c_6a',  'alias' => '6a. XB LN',           'name' =>  '  6.a. Ekspor Barang'],
        ['id' => 'c_6b',  'alias' => '6b. XJ LN',           'name' =>  '  6.b. Ekspor Jasa'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        ['id' => 'c_7a',  'alias' => '7a. MB LN',           'name' =>  '  7.a. Impor Barang'],
        ['id' => 'c_7b',  'alias' => '7b. MJ LN',           'name' =>  '  7.b. Impor Jasa'],
        ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '  8. Net Ekspor Antar Daerah'],
        ['id' => 'c_8a',  'alias' => '8a. X AP',            'name' =>  '  8.a. Ekspor Antar Daerah'],
        ['id' => 'c_8b',  'alias' => '8b. M AP',            'name' =>  '  8.b. Impor Antar Daerah']
    ];

    public $list_wilayah = [
        ['id' => '00', 'alias' => 'Sumsel',         'name' => 'Sumatera Selatan'],
        ['id' => '01', 'alias' => 'OKU',            'name' => 'Ogan Komering Ulu'],
        ['id' => '02', 'alias' => 'OKI',            'name' => 'Ogan Komering Ilir'],
        ['id' => '03', 'alias' => 'Muara Enim',     'name' => 'Muara Enim'],
        ['id' => '04', 'alias' => 'Lahat',          'name' => 'Lahat'],
        ['id' => '05', 'alias' => 'Musi Rawas',     'name' => 'Musi Rawas'],
        ['id' => '06', 'alias' => 'Muba',           'name' => 'Musi Banyuasin'],
        ['id' => '07', 'alias' => 'Banyu Asin',     'name' => 'Banyuasin'],
        ['id' => '08', 'alias' => 'OKUS',           'name' => 'Ogan Komering Ulu Selatan'],
        ['id' => '09', 'alias' => 'OKUT',           'name' => 'Ogan Komering Ulu Timur'],
        ['id' => '10', 'alias' => 'Ogan Ilir',      'name' => 'Ogan Ilir'],
        ['id' => '11', 'alias' => 'Empat Lawang',   'name' => 'Empat Lawang'],
        ['id' => '12', 'alias' => 'PALI',           'name' => 'PALI'],
        ['id' => '13', 'alias' => 'Muratara',       'name' => 'Musi Rawas Utara'],
        ['id' => '71', 'alias' => 'Palembang',      'name' => 'Palembang'],
        ['id' => '72', 'alias' => 'Prabumulih',     'name' => 'Prabumulih'],
        ['id' => '73', 'alias' => 'Pagar Alam',     'name' => 'Pagar Alam'],
        ['id' => '74', 'alias' => 'Lubuk Linggau',  'name' => 'Lubuk Linggau'],
    ];

    public function ringkasan1(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_komponen;
        $list_detail_komponen = $this->list_detail_komponen;
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : $list_periode;
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_pdrb', 'c_1, c_1a, c_1b, c_1d, c_1e, c_1f, c_1g', 'c_2', 'c_3, c_3a, c_3b', 'c_4, c_4a, c_4b', 'c_5', 'c_6, c_6a, c_6b', 'c_7, c_7a, c_7b'];
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
        $data = [];
        foreach ($komponens as $komponen) {
            $row = [];
            $row = [
                'komponen' => $komponen['id'],
                'komponen_name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            // perulangan periode 2024Q1, 2024Q2, 2024Q3 dst
            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);
                if ($id == '1.1') {
                    if (sizeof($arr_periode) > 1) {
                        $data_current = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();
                        $data_prevyear = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();
                        if ($arr_periode[1] != 1) {
                            $data_prevquartile = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();
                        } else {
                            $data_prevquartile = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', 4)->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();
                        }

                        $jml_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();

                        $jml_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();


                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }

                        $data_cumulative = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0])->wherein('q', $q)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();


                        $data_prevcum = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        // dd(null / 12);
                        $row[$periode . 'yoy'] = (
                            $data_current
                            && $data_prevyear
                            && isset($data_prevyear->$komp_id)
                            && $data_prevyear->$komp_id != 0
                        ) ?
                            ($data_prevyear->$komp_id - $data_current->$komp_id) / $data_prevyear->$komp_id : null;

                        $row[$periode . 'qtq'] = (
                            $data_current &&
                            $data_prevquartile &&
                            isset($data_prevquartile->$komp_id) &&
                            $data_prevquartile->$komp_id != 0) ?
                            ($data_prevquartile->$komp_id - $data_current->$komp_id) / $data_prevquartile->$komp_id : null;

                        $row[$periode . 'ctc'] = (
                            $data_cumulative &&
                            $data_prevcum &&
                            isset($data_prevcum->$komp_id) &&
                            $data_prevcum->$komp_id != 0)
                            ? ($data_prevcum->sum($komp_id) - $data_cumulative->sum($komp_id)) / $data_prevcum->sum($komp_id)
                            : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();

                        $jml_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();

                        $data_current = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_prevyear = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();


                        if (
                            $data_current
                            && $data_prevyear
                            && isset($data_prevyear->$komp_id)
                            && $data_prevyear->$komp_id != 0
                        ) {
                            $row[$periode . 'yoy'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                            $row[$periode . 'qtq'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                            $row[$periode . 'ctc'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                        } else {
                            $row[$periode . 'yoy'] = null;
                            $row[$periode . 'qtq'] = null;
                            $row[$periode . 'ctc'] = null;
                        }
                    }
                } else if ($id == '1.2') {
                    if (sizeof($arr_periode) > 1) {
                        $data_hb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();;
                        $data_hk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)->orderBy('revisi_ke', 'desc')->first();;

                        $data_hb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();;

                        $data_hk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)->orderBy('revisi_ke', 'desc')->first();;



                        if ($arr_periode[1] != 1) {
                            // q2-q4
                            $data_hb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();;
                            $data_hk_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 2)->orderBy('revisi_ke', 'desc')->first();;
                        } else {
                            $data_hb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', 4)->where('adhb_or_adhk', 1)->orderBy('revisi_ke', 'desc')->first();
                            $data_hk_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', 4)->where('adhb_or_adhk', 2)->orderBy('revisi_ke', 'desc')->first();
                        }

                        $jml_hb_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_hk_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('q')
                            ->get();

                        $jml_hb_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_hk_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('q')
                            ->get();

                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }

                        $data_hb_c = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0])->wherein('q', $q)
                            ->where(function ($query) use ($jml_hk_q_y) {
                                foreach ($jml_hk_q_y as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_hk_c = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 2)
                            ->where('tahun', $arr_periode[0])->wherein('q', $q)
                            ->where(function ($query) use ($jml_hb_q_y) {
                                foreach ($jml_hb_q_y as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();


                        $data_hb_c_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                            ->where(function ($query) use ($jml_hb_q_y_1) {
                                foreach ($jml_hb_q_y_1 as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $data_hk_c_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                            ->where(function ($query) use ($jml_hk_q_y_1) {
                                foreach ($jml_hk_q_y_1 as $jml_q) {
                                    $query->orWhere(function ($subquery) use ($jml_q) {
                                        $subquery->where('q', $jml_q->q)
                                            ->where('revisi_ke', $jml_q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();



                        $row[$periode . 'yoy'] = ($data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1 && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0 && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0 && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0) ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100) - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) : null;
                        $row[$periode . 'qtq'] = ($data_hb_y && $data_hk_y && $data_hb_q_1 && $data_hk_q_1 && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0 && isset($data_hb_q_1->$komp_id) && $data_hb_q_1->$komp_id != 0 && isset($data_hk_q_1->$komp_id) && $data_hk_q_1->$komp_id != 0) ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100) - ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100)) / ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100) : null;
                        $row[$periode . 'ctc'] = ($data_hb_c && $data_hk_c && $data_hb_c_1 && $data_hk_c_1 && isset($data_hk_c->$komp_id) && $data_hk_c->$komp_id != 0 && isset($data_hb_c_1->$komp_id) && $data_hb_c_1->$komp_id != 0 && isset($data_hk_c_1->$komp_id) && $data_hk_c_1->$komp_id != 0) ? (($data_hb_c->$komp_id / $data_hk_c->$komp_id * 100) - ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100)) / ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100) : null;
                    } else {
                        $jml_hb_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_hk_q_y =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0])
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('q')
                            ->get();

                        $jml_hb_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_hk_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('adhb_or_adhk', 2)
                            ->groupBy('q')
                            ->get();
                        $data_hb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])
                            ->where(function ($query) use ($jml_hb_q_y) {
                                foreach ($jml_hb_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $data_hk_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0])
                            ->where(function ($query) use ($jml_hk_q_y) {
                                foreach ($jml_hk_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();


                        $data_hb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)
                            ->where(function ($query) use ($jml_hb_q_y_1) {
                                foreach ($jml_hb_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $data_hk_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0] - 1)
                            ->where(function ($query) use ($jml_hk_q_y_1) {
                                foreach ($jml_hk_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        // dd($data_hb_y->$komp_id / $data_hk_y->$komp_id);
                        if (
                            $data_hb_y
                            && $data_hk_y
                            && $data_hb_y_1
                            && $data_hk_y_1
                            && isset($data_hk_y->$komp_id)
                            && $data_hk_y->$komp_id != 0
                            && isset($data_hb_y_1->$komp_id)
                            && $data_hb_y_1->$komp_id != 0
                            && isset($data_hk_y_1->$komp_id)
                            && $data_hk_y_1->$komp_id != 0
                        ) {
                            $row[$periode . 'yoy'] = (($data_hb_y->sum($komp_id) / $data_hk_y->sum($komp_id) * 100) - ($data_hb_y_1->sum($komp_id) / $data_hk_y_1->sum($komp_id) * 100)) / ($data_hb_y_1->sum($komp_id) / $data_hb_y_1->sum($komp_id) * 100);
                            $row[$periode . 'qtq'] = (($data_hb_y->sum($komp_id) / $data_hk_y->sum($komp_id) * 100) - ($data_hb_y_1->sum($komp_id) / $data_hk_y_1->sum($komp_id) * 100)) / ($data_hb_y_1->sum($komp_id) / $data_hb_y_1->sum($komp_id) * 100);
                            $row[$periode . 'ctc'] = (($data_hb_y->sum($komp_id) / $data_hk_y->sum($komp_id) * 100) - ($data_hb_y_1->sum($komp_id) / $data_hk_y_1->sum($komp_id) * 100)) / ($data_hb_y_1->sum($komp_id) / $data_hb_y_1->sum($komp_id) * 100);
                            $row[$periode . 'yoy'] = null;
                            $row[$periode . 'qtq'] = null;
                            $row[$periode . 'ctc'] = null;
                        }
                    }
                }
            }
            $data[] = $row;
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan1', compact('list_tabel', 'list_periode', 'list_group_komponen', 'tabel_filter', 'periode_filter', 'komponen_filter', 'data'));
    }

    public function ringkasan2(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_wilayah = $this->list_wilayah;
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.3';
        $periode_filter = $request->periode_filter ? $request->periode_filter : '2024Q2';
        $data = [];
        foreach ($list_wilayah as $wilayah) {
            $row = [];
            $row = [
                'id' => $wilayah['id'],
                'name' => $wilayah['name'],
                'alias' => $wilayah['alias']
            ];
            $arr_periode = explode("Q", $periode_filter);
            if (sizeof($arr_periode) > 1) {
                $data_y = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_y_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_y_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 2)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();

                if ($arr_periode[1] == 1) {
                    $data_q_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->orderBy('revisi_ke', 'desc')->first();
                    $data_q_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1] - 1)->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $data_q_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)->where('q', 4)->orderBy('revisi_ke', 'desc')->first();
                    $data_q_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 2)->where('q', 4)->orderBy('revisi_ke', 'desc')->first();
                }

                $q = [];
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_2 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 2)->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $data_cum_y = Pdrb::select('kode_kab', DB::raw('sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])
                    ->where('q', $q)
                    ->where(function ($query) use ($jml_q_y) {
                        foreach ($jml_q_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_cum_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $q)
                    ->where(function ($query) use ($jml_q_y_1) {
                        foreach ($jml_q_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_cum_y_2 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $q)
                    ->where(function ($query) use ($jml_q_y_2) {
                        foreach ($jml_q_y_2 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_adhk_y = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_adhk_y_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_prov = Pdrb::where('kode_kab', '00')->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();

                $row['yoy_current'] = ($data_y && $data_y_1 && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0) ? round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb, 2) : null;
                $row['yoy_prev']    = ($data_y_1 && $data_y_2 && isset($data_y_2->c_pdrb) && $data_y_2->c_pdrb != 0) ? round(($data_y_1->c_pdrb - $data_y_2->c_pdrb) / $data_y_2->c_pdrb, 2) : null;
                $row['qtq_current'] = ($data_y && $data_q_1 && isset($data_q_1->c_pdrb) && $data_q_1->c_pdrb != 0) ? round(($data_y->c_pdrb - $data_q_1->c_pdrb) / $data_q_1->c_pdrb, 2) : null;
                $row['qtq_prev']    = ($data_y_1 && $data_q_2 && isset($data_q_2->c_pdrb) && $data_q_2->c_pdrb != 0) ? round(($data_y_1->c_pdrb - $data_q_2->c_pdrb) / $data_q_2->c_pdrb, 2) : null;
                $row['ctc_current'] = ($data_cum_y && $data_cum_y_1 && isset($data_cum_y_1->c_pdrb) && $data_cum_y_1->c_pdrb != 0) ? round(($data_cum_y->c_pdrb - $data_cum_y_1->c_pdrb) / $data_cum_y_1->c_pdrb, 2) : null;
                $row['ctc_prev']    = ($data_cum_y_1 && $data_cum_y_2 && isset($data_cum_y_2->c_pdrb) && $data_cum_y_2->c_pdrb != 0) ? round(($data_cum_y_1->c_pdrb - $data_cum_y_2->c_pdrb) / $data_cum_y_2->c_pdrb, 2) : null;
                $row['implisit_yoy'] = ($data_y  && $data_adhk_y && $data_y_1 && $data_adhk_y_1 && isset($data_adhk_y->c_pdrb) && $data_adhk_y->c_pdrb != 0  && isset($data_y_1->c_pdrb) && $data_y_1->c_pdrb != 0  && isset($data_adhk_y_1->c_pdrb) && $data_adhk_y_1->c_pdrb != 0) ? round((($data_y->c_pdrb / $data_adhk_y->c_pdrb * 100) - ($data_y_1->c_pdrb / $data_adhk_y_1->c_pdrb * 100)) / ($data_y_1->c_pdrb / $data_adhk_y_1->c_pdrb * 100), 2) : null;
                $row['share_prov']  = ($data_y  && $data_prov && isset($data_prov->c_pdrb) && $data_prov->c_pdrb != 0) ? round($data_y->c_pdrb / $data_prov->c_pdrb * 100, 2) : null;
            } else {
                $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_2 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 2)->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_prov =  Pdrb::where('kode_kab', '00')
                    ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $data_cum_y = Pdrb::select('kode_kab', DB::raw('sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q_y) {
                        foreach ($jml_q_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_cum_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_y_1) {
                        foreach ($jml_q_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_cum_y_2 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_y_2) {
                        foreach ($jml_q_y_2 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_adhk_cum_y = Pdrb::select('kode_kab', DB::raw('sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q_y) {
                        foreach ($jml_q_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_adhk_cum_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_y_1) {
                        foreach ($jml_q_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_prov_cum = Pdrb::select('kode_kab', DB::raw('sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', "00")->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q_prov) {
                        foreach ($jml_q_prov as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();


                $row['yoy_current'] = $data_cum_y && $data_cum_y_1 && isset($data_cum_y_1->c_pdrb) && $data_cum_y_1->c_pdrb != 0 ? round(($data_cum_y->c_pdrb - $data_cum_y_1->c_pdrb) / $data_cum_y_1->c_pdrb, 2) : null;
                $row['yoy_prev'] = $data_cum_y_1 && $data_cum_y_2 && isset($data_cum_y_2->c_pdrb) && $data_cum_y_2->c_pdrb != 0 ? round(($data_cum_y_1->c_pdrb - $data_cum_y_2->c_pdrb) / $data_cum_y_2->c_pdrb, 2) : null;
                $row['qtq_current'] = $data_cum_y && $data_cum_y_1 && isset($data_cum_y_1->c_pdrb) && $data_cum_y_1->c_pdrb != 0 ? round(($data_cum_y->c_pdrb - $data_cum_y_1->c_pdrb) / $data_cum_y_1->c_pdrb, 2) : null;
                $row['qtq_prev'] = $data_cum_y_1 && $data_cum_y_2 && isset($data_cum_y_2->c_pdrb) && $data_cum_y_2->c_pdrb != 0 ? round(($data_cum_y_1->c_pdrb - $data_cum_y_2->c_pdrb) / $data_cum_y_2->c_pdrb, 2) : null;
                $row['ctc_current'] = $data_cum_y && $data_cum_y_1 && isset($data_cum_y_1->c_pdrb) && $data_cum_y_1->c_pdrb != 0 ? round(($data_cum_y->c_pdrb - $data_cum_y_1->c_pdrb) / $data_cum_y_1->c_pdrb, 2) : null;
                $row['ctc_prev'] = $data_cum_y_1 && $data_cum_y_2 && isset($data_cum_y_2->c_pdrb) && $data_cum_y_2->c_pdrb != 0 ? round(($data_cum_y_1->c_pdrb - $data_cum_y_2->c_pdrb) / $data_cum_y_2->c_pdrb, 2) : null;
                $row['implisit_yoy'] = $data_cum_y  && $data_adhk_cum_y && $data_cum_y_1 && $data_adhk_cum_y_1 && isset($data_adhk_cum_y->c_pdrb) && $data_adhk_cum_y->c_pdrb != 0 && isset($data_cum_y_1->c_pdrb) && $data_cum_y_1->c_pdrb != 0 && isset($data_adhk_cum_y_1->c_pdrb) && $data_adhk_cum_y_1->c_pdrb != 0 ? round((($data_cum_y->c_pdrb / $data_adhk_cum_y->c_pdrb * 100) - ($data_cum_y_1->c_pdrb / $data_adhk_cum_y_1->c_pdrb * 100)) / ($data_cum_y_1->c_pdrb / $data_adhk_cum_y_1->c_pdrb * 100), 2) : null;
                $row['share_prov'] = $data_cum_y  && $data_prov_cum && isset($data_prov_cum->c_pdrb) && $data_prov_cum->c_pdrb != 0 ? round($data_cum_y->c_pdrb / $data_prov_cum->c_pdrb * 100, 2) : null;
            }

            $data[] = $row;
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan2', compact('list_tabel', 'list_periode', 'periode_filter', 'tabel_filter', 'data'));
    }

    public function ringkasan3(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_komponen;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.4';
        $periode_filter = $request->periode_filter ? $request->periode_filter : '2024Q2';
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1', 'c_1a', 'c_1b', 'c_1c', 'c_1d', 'c_1e', 'c_1f', 'c_1g', 'c_2', 'c_3', 'c_3a', 'c_3b', 'c_4', 'c_4a', 'c_4b', 'c_5', 'c_6', 'c_6a', 'c_6b', 'c_7', 'c_7a', 'c_7b', 'c_8', 'c_8a', 'c_8b', 'c_pdrb'];
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
        $data = [];
        foreach ($list_wilayah as $wilayah) {
            $row = [];
            $row = [
                'id' => $wilayah['id'],
                'name' => $wilayah['name'],
                'alias' => $wilayah['alias']
            ];
            $arr_periode = explode("Q", $periode_filter);
            if ($id == "1.4") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();

                    if ($pdrb_y && $pdrb_y_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                        }
                    }
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_y && $pdrb_y_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.5") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();

                    if ($arr_periode[1] == 1) {
                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0] - 1)->where('q', 4)
                            ->orderBy('revisi_ke', 'desc')->first();
                    } else {
                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)
                            ->orderBy('revisi_ke', 'desc')->first();
                    }


                    if ($pdrb_y && $pdrb_q_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id : null;
                        }
                    }
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_y && $pdrb_y_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.6") {
                if (sizeof($arr_periode) > 1) {
                    for ($i = 1; $i <= $arr_periode[1]; $i++) {
                        $q[] = $i;
                    }
                } else {
                    $q = [1, 2, 3, 4];
                }

                $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab',  $wilayah['id'])
                    ->where('tahun', $arr_periode[0])
                    ->wherein('q', $q)
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->where(function ($query) use ($jml_q_y) {
                        foreach ($jml_q_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab',  $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)
                    ->wherein('q', $q)
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->where(function ($query) use ($jml_q_y_1) {
                        foreach ($jml_q_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                if ($pdrb_y && $pdrb_y_1) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                    }
                }
                $data[] = $row;
            } else if ($id == "1.7") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();

                    if ($pdrb_y) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id / $pdrb_y->c_pdrb * 100) : null;
                        }
                    }
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 2)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_y) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.8") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();

                    if ($pdrb_y) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id / $pdrb_y->c_pdrb * 100) : null;
                        }
                    }
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_y) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_y_1->$komp_id) && isset($pdrb_y->c_pdrb) && $pdrb_y->c_pdrb != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.9") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_hb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();
                    $pdrb_hk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();
                    $pdrb_hb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();
                    $pdrb_hk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();


                    if ($pdrb_hb_y && $pdrb_hk_y && $pdrb_hb_y_1 && $pdrb_hk_y_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_hk_y->$komp_id)
                                && $pdrb_hk_y->$komp_id != 0
                                && isset($pdrb_hb_y_1->$komp_id)
                                && $pdrb_hb_y_1->$komp_id != 0
                                && isset($pdrb_hk_y_1->$komp_id)
                                && $pdrb_hk_y_1->$komp_id != 0 ?
                                (($pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id * 100) - ($pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id * 100)) / ($pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id * 100) : null;
                        }
                    }
                } else {
                    $jml_q_hb =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_hk =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $jml_q_hb_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_hk_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();


                    $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hb) {
                            foreach ($jml_q_hb as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hk) {
                            foreach ($jml_q_hk as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hb_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hb_1) {
                            foreach ($jml_q_hb_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hk_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hk_1) {
                            foreach ($jml_q_hk_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_hb && $pdrb_hk && $pdrb_hb_1 && $pdrb_hk_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_hk->$komp_id)
                                && $pdrb_hk->$komp_id != 0
                                && isset($pdrb_hb_1->$komp_id)
                                && $pdrb_hb_1->$komp_id != 0
                                && isset($pdrb_hk_1->$komp_id)
                                && $pdrb_hk_1->$komp_id != 0 ?
                                (($pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100) - ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100)) / ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100) : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.10") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_hb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();
                    $pdrb_hk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->orderby('revisi_ke', 'desc')
                        ->first();


                    if ($arr_periode[1] == 1) {
                        $pdrb_hb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0] - 1)->where('q', 4)
                            ->orderBy('revisi_ke', 'desc')->first();
                        $pdrb_hk_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                            ->where('tahun', $arr_periode[0] - 1)->where('q', 4)
                            ->orderBy('revisi_ke', 'desc')->first();
                    } else {
                        $pdrb_hb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                            ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)
                            ->orderBy('revisi_ke', 'desc')->first();
                        $pdrb_hk_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                            ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)
                            ->orderBy('revisi_ke', 'desc')->first();
                    }

                    if ($pdrb_hb_y && $pdrb_hk_y && $pdrb_hb_q_1 && $pdrb_hk_q_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_hk_y->$komp_id)
                                && $pdrb_hk_y->$komp_id != 0
                                && isset($pdrb_hb_q_1->$komp_id)
                                && $pdrb_hb_q_1->$komp_id != 0
                                && isset($pdrb_hk_q_1->$komp_id)
                                && $pdrb_hk_q_1->$komp_id != 0 ?
                                (($pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id * 100) - ($pdrb_hb_q_1->$komp_id / $pdrb_hk_q_1->$komp_id * 100)) / ($pdrb_hb_q_1->$komp_id / $pdrb_hk_q_1->$komp_id * 100) : null;
                        }
                    }
                } else {
                    $jml_q_hb =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_hk =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $jml_q_hb_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();
                    $jml_q_hk_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();


                    $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hb) {
                            foreach ($jml_q_hb as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hk) {
                            foreach ($jml_q_hk as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hb_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hb_1) {
                            foreach ($jml_q_hb_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_hk_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_hk_1) {
                            foreach ($jml_q_hk_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    if ($pdrb_hb && $pdrb_hk && $pdrb_hb_1 && $pdrb_hk_1) {
                        foreach ($komponens as $komp) {
                            $komp_id = $komp['id'];
                            $row[$komp_id] = isset($pdrb_hk->$komp_id)
                                && $pdrb_hk->$komp_id != 0
                                && isset($pdrb_hb_1->$komp_id)
                                && $pdrb_hb_1->$komp_id != 0
                                && isset($pdrb_hk_1->$komp_id)
                                && $pdrb_hk_1->$komp_id != 0 ?
                                (($pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100) - ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100)) / ($pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100) : null;
                        }
                    }
                }
                $data[] = $row;
            } else if ($id == "1.15") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 1)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 1)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                }
                $data[] = $row;
            } else if ($id == "1.16") {
                if (sizeof($arr_periode) > 1) {
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                        ->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])
                        ->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                        ->where('tahun', $arr_periode[0])->where('adhb_or_adhk', 2)
                        ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah['id'])->where('adhb_or_adhk', 2)
                        ->where('tahun', $arr_periode[0])
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                }
                if ($pdrb_y) {
                    foreach ($komponens as $komp) {
                        $komp_id = $komp['id'];
                        $row[$komp_id] = isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                }
                $data[] = $row;
            }
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan3', compact('list_tabel', 'list_periode', 'list_detail_komponen', 'komponen_filter', 'komponens', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan4(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_komponen;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.4';
        $periode_filter = $request->periode_filter ? $request->periode_filter : ['2024Q1', '2024Q2', '2024Q3', '2024Q4', '2024'];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter :  array_map(function ($item) {
            return $item['column'];
        }, $list_group_komponen);
        $array_komp_filter = [];
        foreach ($komponen_filter as $item) {
            // Pecah elemen berdasarkan koma dan spasi, lalu gabungkan ke array akhir
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
                $tahun = $arr_periode[0];
                $q = sizeof($arr_periode) > 1 ? $arr_periode[1] : "null";

                if ($id == '1.11') {
                    if (sizeof($arr_periode) > 1) {
                        // jika ada Q, misal 2024Q1

                        $data_adhb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();

                        $data_adhk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '2')->first();

                        $data_adhb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun - 1)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();

                        $data_adhk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun - 1)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();
                    } else {
                        $jml_q =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $data_adhb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->where('adhb_or_adhk', 1)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_adhb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $data_adhk_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->where('adhb_or_adhk', 2)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_adhk_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                    }
                } else if ($id == '1.12') {
                    if (sizeof($arr_periode) > 1) {
                        // jika ada Q, misal 2024Q1

                        $data_adhb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();

                        $data_adhk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '2')->first();

                        $data_adhb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun - 1)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();

                        $data_adhk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', '00')->where('tahun', $tahun - 1)->where('q', $q)->orderBy('revisi_ke', 'desc')->where('adhb_or_adhk', '1')->first();
                    } else {
                        $jml_q =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $data_adhb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->where('adhb_or_adhk', 1)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_adhb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $data_adhk_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun)
                            ->where('adhb_or_adhk', 2)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $data_adhk_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $tahun - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where(function ($query) use ($jml_q) {
                                foreach ($jml_q as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                    }
                }

                if ($data_adhb_y && $data_adhb_y_1 && isset($data_adhb_y_1->$komp_id) && $data_adhb_y_1->$komp_id != 0) {
                    $row[$periode . 'adhb'] = ($data_adhb_y->$komp_id - $data_adhb_y_1->$komp_id) / $data_adhb_y_1->$komp_id;
                } else {
                    $row[$periode . 'adhb'] = null;
                }
                if ($data_adhk_y && $data_adhk_y_1  && isset($data_adhk_y_1->$komp_id) && $data_adhk_y_1->$komp_id != 0) {
                    $row[$periode . 'adhk'] = ($data_adhk_y->$komp_id - $data_adhk_y_1->$komp_id) / $data_adhk_y_1->$komp_id;
                } else {
                    $row[$periode . 'adhk'] = null;
                }
            }
            $data[] = $row;
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan4', compact('list_tabel', 'list_periode', 'list_detail_komponen', 'list_group_komponen', 'komponen_filter', 'komponens', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan5(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.4';
        $periode_filter = $request->periode_filter ? $request->periode_filter : '2024Q2';
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = [];

        foreach ($list_detail_komponen as $komp) {
            $row = [
                'id' => $komp['id'],
                'name' => $komp['name'],
                'alias' => $komp['alias']
            ];
            $komp_id = $komp['id'];
            $arr_periode = explode("Q", $periode_filter);

            if (sizeof($arr_periode) > 1) {
                $data_hb_y = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)
                    ->orderBy('revisi_ke', 'desc')->first();

                $data_hb_y_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)
                    ->orderBy('revisi_ke', 'desc')->first();

                if ($arr_periode[1] != 1) {
                    // q2-q4
                    $data_hb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 2)
                        ->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $data_hb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1 = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 2)
                        ->orderBy('revisi_ke', 'desc')->first();
                }

                $jml_hb_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $q = [];

                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $data_hb_c = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)
                    ->where(function ($query) use ($jml_hb_q_y) {
                        foreach ($jml_hb_q_y as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)
                    ->where(function ($query) use ($jml_hk_q_y) {
                        foreach ($jml_hk_q_y as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                    ->where(function ($query) use ($jml_hb_q_y_1) {
                        foreach ($jml_hb_q_y_1 as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                    ->where(function ($query) use ($jml_hk_q_y_1) {
                        foreach ($jml_hk_q_y_1 as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();


                $row['yoy'] = $data_hb_y && $data_hb_y_1 && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0 ? ($data_hb_y->$komp_id - $data_hb_y_1->$komp_id) / $data_hb_y_1->$komp_id : null;
                $row['qtq'] = $data_hb_y && $data_hb_q_1 && isset($data_hb_q_1->$komp_id) && $data_hb_q_1->$komp_id != 0 ? ($data_hb_y->$komp_id - $data_hb_q_1->$komp_id) / $data_hb_q_1->$komp_id : null;
                $row['ctc'] = $data_hb_c && $data_hb_c_1 && isset($data_hb_c_1->$komp_id) && $data_hb_c_1->$komp_id != 0 ? ($data_hb_c->$komp_id - $data_hb_c_1->$komp_id) / $data_hb_c_1->$komp_id : null;
                $row['implisit_yoy'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100)) / ($data_hb_y_1->$komp_id / $data_hk_y_1->$komp_id * 100) : null;
                $row['implisit_qtq'] = $data_hb_y && $data_hk_y && $data_hb_q_1 && $data_hk_q_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_q_1->$komp_id) && $data_hb_q_1->$komp_id != 0   && isset($data_hk_q_1->$komp_id) && $data_hk_q_1->$komp_id != 0   ? (($data_hb_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100)) / ($data_hb_q_1->$komp_id / $data_hk_q_1->$komp_id * 100) : null;
                $row['implisit_ctc'] = $data_hb_c && $data_hk_c && $data_hb_c_1 && $data_hk_c_1  && isset($data_hk_c->$komp_id) && $data_hk_c->$komp_id != 0   && isset($data_hb_c_1->$komp_id) && $data_hb_c_1->$komp_id != 0   && isset($data_hk_c_1->$komp_id) && $data_hk_c_1->$komp_id != 0   ? (($data_hb_c->$komp_id / $data_hk_c->$komp_id * 100)  - ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100)) / ($data_hb_c_1->$komp_id / $data_hk_c_1->$komp_id * 100) : null;
            } else {
                $jml_q_hb_y =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_q_hk_y =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $jml_q_hb_y_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_q_hk_y_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $data_hb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q_hb_y) {
                        foreach ($jml_q_hb_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hk_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q_hk_y) {
                        foreach ($jml_q_hk_y as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab',  $wilayah_filter)->where('adhb_or_adhk', 1)->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_hb_y_1) {
                        foreach ($jml_q_hb_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab',  $wilayah_filter)->where('adhb_or_adhk', 2)->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_hk_y_1) {
                        foreach ($jml_q_hk_y_1 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $row['yoy'] = $data_hb_y && $data_hb_y_1 && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0 ? ($data_hb_y->$komp_id - $data_hb_y_1->$komp_id) / $data_hb_y_1->$komp_id : null;
                $row['qtq'] = $data_hb_y && $data_hb_y_1 && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0 ? ($data_hb_y->$komp_id - $data_hb_y_1->$komp_id) / $data_hb_y_1->$komp_id : null;
                $row['ctc'] = $data_hb_y && $data_hb_y_1 && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0 ? ($data_hb_y->$komp_id - $data_hb_y_1->$komp_id) / $data_hb_y_1->$komp_id : null;
                $row['implisit_yoy'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1 / $data_hk_y_1 * 100)) / ($data_hb_y_1 / $data_hk_y_1 * 100) : null;
                $row['implisit_qtq'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1 / $data_hk_y_1 * 100)) / ($data_hb_y_1 / $data_hk_y_1 * 100) : null;
                $row['implisit_ctc'] = $data_hb_y && $data_hk_y && $data_hb_y_1 && $data_hk_y_1  && isset($data_hk_y->$komp_id) && $data_hk_y->$komp_id != 0   && isset($data_hb_y_1->$komp_id) && $data_hb_y_1->$komp_id != 0   && isset($data_hk_y_1->$komp_id) && $data_hk_y_1->$komp_id != 0   ? (($data_hk_y->$komp_id / $data_hk_y->$komp_id * 100)  - ($data_hb_y_1 / $data_hk_y_1 * 100)) / ($data_hb_y_1 / $data_hk_y_1 * 100) : null;
            }
            $data[] = $row;
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan5', compact('list_tabel', 'list_periode', 'list_wilayah', 'wilayah_filter', 'tabel_filter', 'periode_filter', 'data'));
    }

    public function ringkasan6(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.4';
        $periode_filter = $request->periode_filter ? $request->periode_filter : '2024Q2';
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

        $data = [];

        foreach ($list_detail_komponen as $komp) {
            $row = [
                'id' => $komp['id'],
                'name' => $komp['name'],
                'alias' => $komp['alias']
            ];
            $komp_id = $komp['id'];
            $arr_periode = explode("Q", $periode_filter);

            if (sizeof($arr_periode) > 1) {
                $data_hb_y_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->where('status_data', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)->where('status_data', 1)
                    ->orderBy('revisi_ke', 'desc')->first();

                $data_hb_y_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)
                    ->orderBy('revisi_ke', 'desc')->first();

                $data_hb_y_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)->where('status_data', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)->where('status_data', 1)
                    ->orderBy('revisi_ke', 'desc')->first();

                $data_hb_y_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 1)
                    ->orderBy('revisi_ke', 'desc')->first();
                $data_hk_y_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                    ->where('q', $arr_periode[1])->where('adhb_or_adhk', 2)
                    ->orderBy('revisi_ke', 'desc')->first();

                if ($arr_periode[1] != 1) {
                    $data_hb_q_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 1)->where('status_data', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 2)->where('status_data', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hb_q_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0])
                        ->where('q', $arr_periode[1] - 1)->where('adhb_or_adhk', 2)
                        ->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $data_hb_q_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 1)->where('status_data', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1_rilis = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 2)->where('status_data', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hb_q_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 1)
                        ->orderBy('revisi_ke', 'desc')->first();
                    $data_hk_q_1_revisi = Pdrb::where('kode_kab', $wilayah_filter)->where('tahun', $arr_periode[0] - 1)
                        ->where('q', 4)->where('adhb_or_adhk', 2)
                        ->orderBy('revisi_ke', 'desc')->first();
                }
                $jml_hb_q_y_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_1_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_1_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_1_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_1_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $q = [];

                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $data_hb_c_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hb_q_y_rilis) {
                        foreach ($jml_hb_q_y_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hk_c_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hk_q_y_rilis) {
                        foreach ($jml_hk_q_y_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)
                    ->where(function ($query) use ($jml_hb_q_y_revisi) {
                        foreach ($jml_hb_q_y_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0])->wherein('q', $q)
                    ->where(function ($query) use ($jml_hk_q_y_revisi) {
                        foreach ($jml_hk_q_y_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_1_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hb_q_y_1_rilis) {
                        foreach ($jml_hb_q_y_1_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_1_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hk_q_y_1_rilis) {
                        foreach ($jml_hk_q_y_1_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hb_c_1_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                    ->where(function ($query) use ($jml_hb_q_y_1_revisi) {
                        foreach ($jml_hb_q_y_1_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_1_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0] - 1)->wherein('q', $q)
                    ->where(function ($query) use ($jml_hk_q_y_1_revisi) {
                        foreach ($jml_hk_q_y_1_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();


                $row['yoy_rilis'] = $data_hb_y_rilis && $data_hb_y_1_rilis && isset($data_hb_y_1_rilis->$komp_id) && $data_hb_y_1_rilis->$komp_id != 0 ? ($data_hb_y_rilis->$komp_id - $data_hb_y_1_rilis->$komp_id) / $data_hb_y_1_rilis->$komp_id : null;
                $row['yoy_revisi'] = $data_hb_y_revisi && $data_hb_y_1_revisi && isset($data_hb_y_1_revisi->$komp_id) && $data_hb_y_1_revisi->$komp_id != 0 ? ($data_hb_y_revisi->$komp_id - $data_hb_y_1_revisi->$komp_id) / $data_hb_y_1_revisi->$komp_id : null;

                $row['qtq_rilis'] = $data_hb_y_rilis && $data_hb_q_1_rilis && isset($data_hb_q_1_rilis->$komp_id) && $data_hb_q_1_rilis->$komp_id != 0 ? ($data_hb_y_rilis->$komp_id - $data_hb_q_1_rilis->$komp_id) / $data_hb_q_1_rilis->$komp_id : null;
                $row['qtq_revisi'] = $data_hb_y_revisi && $data_hb_q_1_revisi  && isset($data_hb_q_1_revisi->$komp_id) && $data_hb_q_1_revisi->$komp_id != 0 ? ($data_hb_y_revisi->$komp_id - $data_hb_q_1_revisi->$komp_id) / $data_hb_q_1_revisi->$komp_id : null;

                $row['ctc_rilis'] = $data_hb_c_rilis && $data_hb_c_1_rilis && isset($data_hb_c_1_rilis->$komp_id) && $data_hb_c_1_rilis->$komp_id != 0 ? ($data_hb_c_rilis->$komp_id - $data_hb_c_1_rilis->$komp_id) / $data_hb_c_1_rilis->$komp_id : null;
                $row['ctc_revisi'] = $data_hb_c_revisi && $data_hb_c_1_revisi  && isset($data_hb_c_1_revisi->$komp_id) && $data_hb_c_1_revisi->$komp_id != 0 ? ($data_hb_c_revisi->$komp_id - $data_hb_c_1_revisi->$komp_id) / $data_hb_c_1_revisi->$komp_id : null;

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
                    (($data_hb_y_rilis->$komp_id / $data_hk_y_rilis->$komp_id * 100) - ($data_hb_y_1_rilis->$komp_id / $data_hk_y_1_rilis->$komp_id * 100)) / ($data_hb_y_1_rilis->$komp_id / $data_hk_y_1_rilis->$komp_id * 100)
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
                    (($data_hb_y_revisi->$komp_id / $data_hk_y_revisi->$komp_id * 100) - ($data_hb_y_1_revisi->$komp_id / $data_hk_y_1_revisi->$komp_id * 100)) / ($data_hb_y_1_revisi->$komp_id / $data_hk_y_1_revisi->$komp_id * 100)
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
                    (($data_hb_y_rilis->$komp_id / $data_hk_y_rilis->$komp_id * 100) - ($data_hb_q_1_rilis->$komp_id / $data_hk_q_1_rilis->$komp_id * 100)) / ($data_hb_q_1_rilis->$komp_id / $data_hk_q_1_rilis->$komp_id * 100)
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
                    (($data_hb_y_revisi->$komp_id / $data_hk_y_revisi->$komp_id * 100) - ($data_hb_q_1_revisi->$komp_id / $data_hk_q_1_revisi->$komp_id * 100)) / ($data_hb_q_1_revisi->$komp_id / $data_hk_q_1_revisi->$komp_id * 100)
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
                    (($data_hb_c_rilis->$komp_id / $data_hk_c_rilis->$komp_id * 100) - ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)) / ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)
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
                    (($data_hb_c_revisi->$komp_id / $data_hk_c_revisi->$komp_id * 100) - ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)) / ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)
                    : null;
            } else {
                $jml_hb_q_y_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hb_q_y_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_1_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_1_rilis =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->where('status_data', 1)
                    ->groupBy('q')
                    ->get();

                $jml_hb_q_y_1_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 1)
                    ->groupBy('q')
                    ->get();
                $jml_hk_q_y_1_revisi =  Pdrb::where('kode_kab', $wilayah_filter)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->where('adhb_or_adhk', 2)
                    ->groupBy('q')
                    ->get();

                $data_hb_c_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])->where('status_data', 1)
                    ->where(function ($query) use ($jml_hb_q_y_rilis) {
                        foreach ($jml_hb_q_y_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hk_c_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0])->where('status_data', 1)
                    ->where(function ($query) use ($jml_hk_q_y_rilis) {
                        foreach ($jml_hk_q_y_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_hb_q_y_revisi) {
                        foreach ($jml_hb_q_y_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_hk_q_y_revisi) {
                        foreach ($jml_hk_q_y_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_1_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hb_q_y_1_rilis) {
                        foreach ($jml_hb_q_y_1_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();
                $data_hk_c_1_rilis = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0] - 1)->where('status_data', 1)
                    ->where(function ($query) use ($jml_hk_q_y_1_rilis) {
                        foreach ($jml_hk_q_y_1_rilis as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hb_c_1_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 1)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_hb_q_y_1_revisi) {
                        foreach ($jml_hb_q_y_1_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $data_hk_c_1_revisi = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah_filter)->where('adhb_or_adhk', 2)
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_hk_q_y_1_revisi) {
                        foreach ($jml_hk_q_y_1_revisi as $jml_q) {
                            $query->orWhere(function ($subquery) use ($jml_q) {
                                $subquery->where('q', $jml_q->q)
                                    ->where('revisi_ke', $jml_q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();


                $row['yoy_rilis'] = $data_hb_c_rilis && $data_hb_c_1_rilis && isset($data_hb_c_1_rilis->$komp_id) && $data_hb_c_1_rilis->$komp_id != 0 ? ($data_hb_c_rilis->$komp_id - $data_hb_c_1_rilis->$komp_id) / $data_hb_c_1_rilis->$komp_id : null;
                $row['yoy_revisi'] = $data_hb_c_revisi && $data_hb_c_1_revisi && isset($data_hb_c_1_revisi->$komp_id) && $data_hb_c_1_revisi->$komp_id != 0 ? ($data_hb_c_revisi->$komp_id - $data_hb_c_1_revisi->$komp_id) / $data_hb_c_1_revisi->$komp_id : null;

                $row['qtq_rilis'] = $data_hb_c_rilis && $data_hb_c_1_rilis && isset($data_hb_c_1_rilis->$komp_id) && $data_hb_c_1_rilis->$komp_id != 0 ? ($data_hb_c_rilis->$komp_id - $data_hb_c_1_rilis->$komp_id) / $data_hb_c_1_rilis->$komp_id : null;
                $row['qtq_revisi'] = $data_hb_c_revisi && $data_hb_c_1_revisi && isset($data_hb_c_1_revisi->$komp_id) && $data_hb_c_1_revisi->$komp_id != 0 ? ($data_hb_c_revisi->$komp_id - $data_hb_c_1_revisi->$komp_id) / $data_hb_c_1_revisi->$komp_id : null;

                $row['ctc_rilis'] = $data_hb_c_rilis && $data_hb_c_1_rilis && isset($data_hb_c_1_rilis->$komp_id) && $data_hb_c_1_rilis->$komp_id != 0 ? ($data_hb_c_rilis->$komp_id - $data_hb_c_1_rilis->$komp_id) / $data_hb_c_1_rilis->$komp_id : null;
                $row['ctc_revisi'] = $data_hb_c_revisi && $data_hb_c_1_revisi && isset($data_hb_c_1_revisi->$komp_id) && $data_hb_c_1_revisi->$komp_id != 0 ? ($data_hb_c_revisi->$komp_id - $data_hb_c_1_revisi->$komp_id) / $data_hb_c_1_revisi->$komp_id : null;

                $row['implisit_yoy_rilis'] = $data_hb_c_rilis
                    && $data_hk_c_rilis
                    && $data_hb_c_1_rilis
                    && $data_hk_c_1_rilis
                    && isset($data_hk_c_rilis->$komp_id)
                    && $data_hk_c_rilis->$komp_id != 0
                    && isset($data_hb_c_1_rilis->$komp_id)
                    && $data_hb_c_1_rilis->$komp_id != 0
                    && isset($data_hk_c_1_rilis->$komp_id)
                    && $data_hk_c_1_rilis->$komp_id != 0 ?
                    (($data_hb_c_rilis->$komp_id / $data_hk_c_rilis->$komp_id * 100) - ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)) / ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)
                    : null;
                $row['implisit_yoy_revisi'] = $data_hb_c_revisi
                    && $data_hk_c_revisi
                    && $data_hb_c_1_revisi
                    && $data_hk_c_1_revisi
                    && isset($data_hk_c_revisi->$komp_id)
                    && $data_hk_c_revisi->$komp_id != 0
                    && isset($data_hb_c_1_revisi->$komp_id)
                    && $data_hb_c_1_revisi->$komp_id != 0
                    && isset($data_hk_c_1_revisi->$komp_id)
                    && $data_hk_c_1_revisi->$komp_id != 0 ?
                    (($data_hb_c_revisi->$komp_id / $data_hk_c_revisi->$komp_id * 100) - ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)) / ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)
                    : null;

                $row['implisit_qtq_rilis'] = $data_hb_c_rilis
                    && $data_hk_c_rilis
                    && $data_hb_c_1_rilis
                    && $data_hk_c_1_rilis
                    && isset($data_hk_c_rilis->$komp_id)
                    && $data_hk_c_rilis->$komp_id != 0
                    && isset($data_hb_c_1_rilis->$komp_id)
                    && $data_hb_c_1_rilis->$komp_id != 0
                    && isset($data_hk_c_1_rilis->$komp_id)
                    && $data_hk_c_1_rilis->$komp_id != 0 ?
                    (($data_hb_c_rilis->$komp_id / $data_hk_c_rilis->$komp_id * 100) - ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)) / ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)
                    : null;
                $row['implisit_qtq_revisi'] = $data_hb_c_revisi
                    && $data_hk_c_revisi
                    && $data_hb_c_1_revisi
                    && $data_hk_c_1_revisi
                    && isset($data_hk_c_revisi->$komp_id)
                    && $data_hk_c_revisi->$komp_id != 0
                    && isset($data_hb_c_1_revisi->$komp_id)
                    && $data_hb_c_1_revisi->$komp_id != 0
                    && isset($data_hk_c_1_revisi->$komp_id)
                    && $data_hk_c_1_revisi->$komp_id != 0 ?
                    (($data_hb_c_revisi->$komp_id / $data_hk_c_revisi->$komp_id * 100) - ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)) / ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)
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
                    (($data_hb_c_rilis->$komp_id / $data_hk_c_rilis->$komp_id * 100) - ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)) / ($data_hb_c_1_rilis->$komp_id / $data_hk_c_1_rilis->$komp_id * 100)
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
                    (($data_hb_c_revisi->$komp_id / $data_hk_c_revisi->$komp_id * 100) - ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)) / ($data_hb_c_1_revisi->$komp_id / $data_hk_c_1_revisi->$komp_id * 100)
                    : null;
            }
            $data[] = $row;
        }
        // $data = [];
        // dd($data);
        return view('pdrb_ringkasan.ringkasan6', compact('list_tabel', 'list_periode', 'list_wilayah', 'wilayah_filter', 'tabel_filter', 'periode_filter', 'data'));
    }
}
