<?php

namespace App\Http\Controllers;

use App\Pdrb;
use App\SettingApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabelKabkotController extends Controller
{
    public $list_wilayah;
    public $tahun_berlaku;
    public $triwulan_berlaku;
    public $list_periode = [];

    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
        $this->tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first()->setting_value;
        $this->triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first()->setting_value;
        for ($i = 3; $i >= 0; $i--) {
            $tahun = $this->tahun_berlaku - $i;
            array_push($this->list_periode, "{$tahun}Q1");
            array_push($this->list_periode, "{$tahun}Q2");
            array_push($this->list_periode, "{$tahun}Q3");
            array_push($this->list_periode, "{$tahun}Q4");
            array_push($this->list_periode, "{$tahun}");
        }
    }

    public $list_tabel = [
        [
            'id' => '3.1',
            'name' => 'Tabel 3.1. PDRB ADHB Menurut Pengeluaran, (Juta Rp)'
        ],
        [
            'id' => '3.2',
            'name' => 'Tabel 3.2. PDRB ADHK Menurut Pengeluaran, (Juta Rp)'
        ],
        [
            'id' => '3.3',
            'name' => 'Tabel 3.3. Pertumbuhan PDRB (Q-TO-Q)'
        ],
        [
            'id' => '3.4',
            'name' => 'Tabel 3.4. Pertumbuhan PDRB (Y-ON-Y)'
        ],
        [
            'id' => '3.5',
            'name' => 'Tabel 3.5. Pertumbuhan PDRB (C-TO-C)'
        ],
        [
            'id' => '3.6',
            'name' => 'Tabel 3.6. Indeks Implisit'
        ],
        [
            'id' => '3.7',
            'name' => 'Tabel 3.7. Pertumbuhan Indeks Implisit (Y-on-Y)'
        ],
        [
            'id' => '3.8',
            'name' => 'Tabel 3.8. Sumber Pertumbuhan (Q-to-Q)'
        ],
        [
            'id' => '3.9',
            'name' => 'Tabel 3.9. Sumber Pertumbuhan (Y-on-Y)'
        ],
        [
            'id' => '3.10',
            'name' => 'Tabel 3.10. Sumber Pertumbuhan (C-to-C)'
        ],

    ];

    public $list_group_12_pkrt = [
        ['column' => "c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g, c_1h, c_1i, c_1j, c_1k, c_1l", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3, c_3a, c_3b", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4, c_4a, c_4b", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_5", 'name' => '5. Perubahan Inventori'],
        ['column' => "c_6, c_6a, c_6b", 'name' => '6. Ekspor Luar Negeri'],
        ['column' => "c_7, c_7a, c_7b", 'name' => '7. Impor Luar Negeri'],
        ['column' => "c_8, c_8a, c_8b", 'name' => '8. Net Ekspor Antar Daerah'],
        ['column' => "c_pdrb", 'name' => '9. PDRB'],
    ];

    public $list_group_7_pkrt = [
        ['column' => "c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3, c_3a, c_3b", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4, c_4a, c_4b", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_5", 'name' => '5. Perubahan Inventori'],
        ['column' => "c_6, c_6a, c_6b", 'name' => '6. Ekspor Luar Negeri'],
        ['column' => "c_7, c_7a, c_7b", 'name' => '7. Impor Luar Negeri'],
        ['column' => "c_8, c_8a, c_8b", 'name' => '8. Net Ekspor Antar Daerah'],
        ['column' => "c_pdrb", 'name' => '9. PDRB'],
    ];

    public $list_group_rilis_komponen = [
        ['column' => "c_1", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_pdrb", 'name' => '5. PDRB'],
    ];

    public $list_detail_komponen = [
        ['id' => 'c_pdrb', 'alias' => 'PDRB',               'name' => 'PDRB'],
        ['id' => 'c_1',   'alias' => '1. PKRT',             'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['id' => 'c_1a',  'alias' => '1a. PKRT-Mamin  ',    'name' => '1.a. Makanan dan Minuman Non Beralkohol'],
        ['id' => 'c_1b',  'alias' => '1b. PKRT-MinRok',     'name' => '1.b. Minuman Beralkohol dan Rokok'],
        ['id' => 'c_1c',  'alias' => '1c. PKRT-Pakaian',    'name' => '1.c. Pakaian'],
        ['id' => 'c_1d',  'alias' => '1d. PKRT-Energi ',    'name' => '1.d. Perumahan, Air, Listrik, Gas dan Bahan Bakar Lainnya'],
        ['id' => 'c_1e',  'alias' => '1e. PKRT-Perumah',    'name' => '1.e. Perabot, Peralatan rumahtangga dan Pemeliharaan Rutin Rumah'],
        ['id' => 'c_1f',  'alias' => '1f. PKRT-Kesehatan ', 'name' => '1.f. Kesehatan'],
        ['id' => 'c_1g',  'alias' => '1g. PKRT-Transport',  'name' => '1.g. Transportasi/Angkutan'],
        ['id' => 'c_1h',  'alias' => '1g. PKRT-Komunikasi', 'name' => '1.h. Komunikasi'],
        ['id' => 'c_1i',  'alias' => '1g. PKRT-Rekreasi',   'name' => '1.i. Rekreasi dan Budaya'],
        ['id' => 'c_1j',  'alias' => '1g. PKRT-Pendidikan', 'name' => '1.j. Pendidikan'],
        ['id' => 'c_1k',  'alias' => '1g. PKRT-Hotel',      'name' => '1.k. Penginapan dan Hotel'],
        ['id' => 'c_1l',  'alias' => '1g. PKRT-Jasa',       'name' => '1.l. Barang Pribadi dan Jasa Perorangan'],
        ['id' => 'c_2',   'alias' => '2. PKLNPRT',          'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['id' => 'c_3',   'alias' => '3.PKP',               'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['id' => 'c_3a',  'alias' => '3a.PKP-Kol',          'name' => '3.a. Konsumsi Kolektif'],
        ['id' => 'c_3b',  'alias' => '3b.PKP-Ind',          'name' => '3.b. Konsumsi Individu'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_4a',  'alias' => '4a. PMTB-Bang',       'name' => '4.a. Bangunan'],
        ['id' => 'c_4b',  'alias' => '4b. PMTB-NB',         'name' => '4.b. Non Bangunan'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        ['id' => 'c_6a',  'alias' => '6a. XB LN',           'name' => '6.a. Ekspor Barang'],
        ['id' => 'c_6b',  'alias' => '6b. XJ LN',           'name' => '6.b. Ekspor Jasa'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        ['id' => 'c_7a',  'alias' => '7a. MB LN',           'name' => '7.a. Impor Barang'],
        ['id' => 'c_7b',  'alias' => '7b. MJ LN',           'name' => '7.b. Impor Jasa'],
        ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '8. Net Ekspor Antar Daerah'],
        ['id' => 'c_8a',  'alias' => '8a. X AP',            'name' => '8.a. Ekspor Antar Daerah'],
        ['id' => 'c_8b',  'alias' => '8b. M AP',            'name' => '8.b. Impor Antar Daerah']
    ];

    public $list_detail_komponen_7pkrt = [
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

    public $list_detail_komponen_brs = [
        ['id' => 'c_1',   'alias' => '1. PKRT',             'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['id' => 'c_2',   'alias' => '2. PKLNPRT',          'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['id' => 'c_3',   'alias' => '3.PKP',               'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '  8. Net Ekspor Antar Daerah'],
        ['id' => 'c_pdrb', 'alias' => 'PDRB',               'name' =>  'PDRB'],
    ];

    public $list_detail_komponen_rilis = [
        ['id' => 'c_1',     'alias' => '1. Kon Non Publik',  'name' => '1. Konsumsi Akhir Non Publik'],
        ['id' => 'c_2',     'alias' => '2. Kon Publik',      'name' => '2. Konsumsi Akhir Publik'],
        ['id' => 'c_3',     'alias' => '3.Inves',            'name' => '3. Investasi'],
        ['id' => 'c_4',     'alias' => '4. Lainnya',         'name' => '4. Lainnya'],
        ['id' => 'c_pdrb',  'alias' => 'PDRB',               'name' => 'PDRB'],
    ];

    public function kabkot(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_12_pkrt;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g, c_1h, c_1i, c_1j, c_1k, c_1l', 'c_2', 'c_3, c_3a, c_3b', 'c_4, c_4a, c_4b', 'c_5', 'c_6, c_6a, c_6b', 'c_7, c_7a, c_7b', 'c_8, c_8a, c_8b', 'c_pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';

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
                'id' => $komponen['id'],
                'name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);

                if ($id === '3.1') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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
                        $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.2') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.3') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.4') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.5') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                } else if ($id === '3.6') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    }
                } else if ($id === '3.7') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hb_y_1 = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y_1 = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $implisit_y = $pdrb_hb_y && $pdrb_hk_y && isset($pdrb_hk_y->$komp_id) && $pdrb_hk_y->$komp_id != 0 ? $pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_y_1 && $pdrb_hk_y_1  && isset($pdrb_hk_y_1->$komp_id) && $pdrb_hk_y_1->$komp_id != 0 ? $pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1  && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $jml_q_hb_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hb_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $implisit_y = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0  ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_1 && $pdrb_hk_1  && isset($pdrb_hk_1->$komp_id) && $pdrb_hk_1->$komp_id != 0 ? $pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 * 100 : null;
                    }
                } else if ($id === '3.8') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_q_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_q_1) {
                                foreach ($rev_q_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.9') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y_1) {
                                foreach ($rev_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.10') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y) {
                            foreach ($rev_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g,sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y_1) {
                            foreach ($rev_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                    $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                }
            }
            $data[] = $row;
        }
        // dd($data);
        return view('pdrb_kabkot.index', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }

    public function kabkot_7pkrt(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_7_pkrt;
        $list_detail_komponen = $this->list_detail_komponen_7pkrt;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';  //'c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g'
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g', 'c_2', 'c_3, c_3a, c_3b', 'c_4, c_4a, c_4b', 'c_5', 'c_6, c_6a, c_6b', 'c_7, c_7a, c_7b', 'c_8, c_8a, c_8b', 'c_pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';
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
                'id' => $komponen['id'],
                'name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {

                $arr_periode = explode("Q", $periode);
                if ($id === '3.1') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.2') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.3') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
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
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.4') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
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
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0  ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.5') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
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
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0  ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                } else if ($id === '3.6') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_hb && $pdrb_hk  && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0  ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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
                            ->where('kode_kab', $wilayah_filter)
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

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0  ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    }
                } else if ($id === '3.7') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $implisit_y = $pdrb_hb_y && $pdrb_hk_y  && isset($pdrb_hk_y->$komp_id) && $pdrb_hk_y->$komp_id != 0  ? $pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_y_1 && $pdrb_hk_y_1  && isset($pdrb_hk_y_1->$komp_id) && $pdrb_hk_y_1->$komp_id != 0  ? $pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id  * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0  ? ($implisit_y - $implisit_y_1) / $implisit_y_1  * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $jml_q_hb_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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
                            ->where('kode_kab', $wilayah_filter)
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
                            ->where('kode_kab', $wilayah_filter)
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
                            ->where('kode_kab', $wilayah_filter)
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

                        $implisit_y = $pdrb_hb && $pdrb_hk  && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_1 && $pdrb_hk_1   && isset($pdrb_hk_1->$komp_id) && $pdrb_hk_1->$komp_id != 0 ? $pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 * 100 : null;
                    }
                } else if ($id === '3.8') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_q_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_q_1) {
                                foreach ($rev_q_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.9') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y_1) {
                                foreach ($rev_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.10') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y) {
                            foreach ($rev_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {

                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y_1) {
                            foreach ($rev_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                }
            }
            $data[] = $row;
        }
        return view('pdrb_kabkot.kabkot_7pkrt', compact('list_tabel', 'tahun_berlaku',  'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }

    public function kabkot_brs(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_7_pkrt;
        $list_detail_komponen = $this->list_detail_komponen_brs;
        $list_wilayah = $this->list_wilayah;
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : $list_periode;
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1, c_1a, c_1b, c_1d, c_1e, c_1f, c_1g', 'c_2', 'c_3, c_3a, c_3b', 'c_4, c_4a, c_4b', 'c_5', 'c_6, c_6a, c_6b', 'c_7, c_7a, c_7b', 'c_8, c_8a, c_8b', 'c_pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';
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
                'id' => $komponen['id'],
                'name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {
                $arr_periode = explode("Q", $periode);
                if ($id === '3.1') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.2') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.3') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 1)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 1)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
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
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                    }
                } else if ($id === '3.4') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
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
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                    }
                } else if ($id === '3.5') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)

                        ->where('adhb_or_adhk', 1)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
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

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
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
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0  ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id : null;
                } else if ($id === '3.6') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    }
                } else if ($id === '3.7') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $implisit_y = $pdrb_hb_y && $pdrb_hk_y && isset($pdrb_hk_y->$komp_id) && $pdrb_hk_y->$komp_id != 0 ? $pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id : null;
                        $implisit_y_1 = $pdrb_hb_y_1 && $pdrb_hk_y_1 && isset($pdrb_hk_y_1->$komp_id) && $pdrb_hk_y_1->$komp_id != 0 ? $pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id : null;
                        $row[$periode] = $implisit_y && $implisit_y_1  && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 : null;
                    } else {

                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $jml_q_hb_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hb_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $implisit_y = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id : null;
                        $implisit_y_1 = $pdrb_hb_1 && $pdrb_hk_1 && isset($pdrb_hk_1->$komp_id) && $pdrb_hk_1->$komp_id != 0 ? $pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 : null;
                    }
                } else if ($id === '3.8') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                            $pdrb_prov_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', '00')
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();

                            $pdrb_prov_q_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                                ->where('kode_kab', '00')
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $pdrb_prov_q_1 && isset($pdrb_prov_q_1->$komp_id) && $pdrb_prov_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_prov_q_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_prov_q_y_1 =  Pdrb::where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $pdrb_prov_q_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_prov_q_y_1) {
                                foreach ($jml_prov_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $pdrb_prov_q_1 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_prov_q_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.9') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_prov_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_2, c_3, c_4, c_5, c_6, c_7, c_8, c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $pdrb_prov_y_1   && isset($pdrb_prov_y_1->$komp_id) && $pdrb_prov_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_prov_y_1->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
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
                        $pdrb_prov_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', '00')
                            ->where('tahun', $arr_periode[0] - 1)
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
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $pdrb_prov_y_1 && isset($pdrb_prov_y_1->$komp_id) && $pdrb_prov_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_prov_y_1->$komp_id : null;
                    }
                } else if ($id === '3.10') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_prov_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', '00')
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();
                    $pdrb_prov_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) as c_4, sum(c_5) as c_5,sum(c_6) as c_6, sum(c_7) as c_7, sum(c_8) as c_8, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', '00')
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_prov_q_y_1) {
                            foreach ($jml_prov_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $pdrb_prov_y_1 && isset($pdrb_prov_y_1->$komp_id) && $pdrb_prov_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_prov_y_1->$komp_id : null;
                }
            }
            $data[] = $row;
        }
        return view('pdrb_kabkot.kabkot_brs', compact('list_tabel', 'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }

    public function kabkot_rilis(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_rilis_komponen;
        $list_detail_komponen = $this->list_detail_komponen_rilis;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1', 'c_2', 'c_3', 'c_4', 'c_pdrb'];
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';
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
                'id' => $komponen['id'],
                'name' => $komponen['name'],
            ];
            $komp_id = $komponen['id'];
            foreach ($periode_filter as $periode) {

                $arr_periode = explode("Q", $periode);
                if ($id === '3.1') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
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
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.2') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    } else {
                        $jml_q_y =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y ? $pdrb_y->$komp_id : null;
                    }
                } else if ($id === '3.3') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.4') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    } else {
                        $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();

                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y) {
                                foreach ($jml_q_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($jml_q_y_1) {
                                foreach ($jml_q_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0  ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                    }
                } else if ($id === '3.5') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $jml_q_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $jml_q_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();

                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y) {
                            foreach ($jml_q_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($jml_q_y_1) {
                            foreach ($jml_q_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $row[$periode] = $pdrb_y && $pdrb_y_1  && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0  ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                } else if ($id === '3.6') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $row[$periode] = $pdrb_hb && $pdrb_hk  && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0  ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0  ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                    }
                } else if ($id === '3.7') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hb_y_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $pdrb_hk_y_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $implisit_y = $pdrb_hb_y && $pdrb_hk_y  && isset($pdrb_hk_y->$komp_id) && $pdrb_hk_y->$komp_id != 0  ? $pdrb_hb_y->$komp_id / $pdrb_hk_y->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_y_1 && $pdrb_hk_y_1  && isset($pdrb_hk_y_1->$komp_id) && $pdrb_hk_y_1->$komp_id != 0  ? $pdrb_hb_y_1->$komp_id / $pdrb_hk_y_1->$komp_id  * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0  ? ($implisit_y - $implisit_y_1) / $implisit_y_1  * 100 : null;
                    } else {
                        $jml_q_hb =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();

                        $jml_q_hb_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 1)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();
                        $jml_q_hk_1 =  Pdrb::where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->groupBy('q')
                            ->get();


                        $pdrb_hb = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hb_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $pdrb_hk_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
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

                        $implisit_y = $pdrb_hb && $pdrb_hk  && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                        $implisit_y_1 = $pdrb_hb_1 && $pdrb_hk_1   && isset($pdrb_hk_1->$komp_id) && $pdrb_hk_1->$komp_id != 0 ? $pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100 : null;
                        $row[$periode] = $implisit_y && $implisit_y_1 && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 * 100 : null;
                    }
                } else if ($id === '3.8') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        if ($arr_periode[1] == 1) {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0] - 1)
                                ->where('q', 4)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        } else {
                            $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('(c_1 + c_2) as c_1 , c_3 as c_2, c_4 as c_3, (c_6- c_7+ c_8) as c_4, c_pdrb'))
                                ->where('kode_kab', $wilayah_filter)
                                ->where('tahun', $arr_periode[0])
                                ->where('q', $arr_periode[1] - 1)
                                ->where('adhb_or_adhk', 2)
                                ->where('status_data', 1)
                                ->orderby('revisi_ke', 'desc')
                                ->first();
                        }
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_q_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_q_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_q_1) {
                                foreach ($rev_q_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.9') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('c_1 , c_1a + c_1b as c_1a, c_1c as c_1b , c_1d + c_1e as c_1c, c_1f+c_1j as c_1d, c_1g+c_1h+c_1i as c_1e, c_1k as c_1f, c_1l as c_1g, c_2, c_3, c_3a, c_3b, c_4, c_4a, c_4b, c_5, c_6, c_6a, c_6b, c_7, c_7a, c_7b, c_8, c_8a, c_8b, c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('q', $arr_periode[1])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->orderby('revisi_ke', 'desc')
                            ->first();
                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    } else {
                        $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->groupBy('q')
                            ->get();
                        $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0])
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y) {
                                foreach ($rev_y as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                            ->where('kode_kab', $wilayah_filter)
                            ->where('tahun', $arr_periode[0] - 1)
                            ->where('adhb_or_adhk', 2)
                            ->where('status_data', 1)
                            ->where(function ($query) use ($rev_y_1) {
                                foreach ($rev_y_1 as $q) {
                                    $query->orWhere(function ($subquery) use ($q) {
                                        $subquery->where('q', $q->q)
                                            ->where('revisi_ke', $q->max_revisi);
                                    });
                                }
                            })->groupBy('kode_kab')->first();

                        $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                        $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                            && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                            ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                    }
                } else if ($id === '3.10') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }

                    $rev_y =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $rev_y_1 =  Pdrb::selectRaw('q, MAX(revisi_ke) as max_revisi')
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->groupBy('q')
                        ->get();
                    $pdrb_y = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0])
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y) {
                            foreach ($rev_y as $q) {
                                $query->orWhere(function ($subquery) use ($q) {

                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $pdrb_y_1 = Pdrb::select('kode_kab', DB::raw('sum(c_1 + c_2) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6- c_7+ c_8) as c_4, sum(c_pdrb) as c_pdrb'))
                        ->where('kode_kab', $wilayah_filter)
                        ->where('tahun', $arr_periode[0] - 1)
                        ->wherein('q', $q)
                        ->where('adhb_or_adhk', 2)
                        ->where('status_data', 1)
                        ->where(function ($query) use ($rev_y_1) {
                            foreach ($rev_y_1 as $q) {
                                $query->orWhere(function ($subquery) use ($q) {
                                    $subquery->where('q', $q->q)
                                        ->where('revisi_ke', $q->max_revisi);
                                });
                            }
                        })->groupBy('kode_kab')->first();

                    $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                }
            }
            $data[] = $row;
        }
        return view('pdrb_kabkot.kabkot_rilis', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }
}
