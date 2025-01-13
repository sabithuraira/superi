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
    public $select_12pkrt = [];
    public $select_7pkrt = [];
    public $select_rilis = [];

    public function __construct()
    {
        $this->list_wilayah = config("app.wilayah");
        $this->tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first()->setting_value;
        $this->triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first()->setting_value;
        for ($i = 2010; $i <= $this->tahun_berlaku; $i++) {
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
        $this->select_12pkrt =  ['kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_1h) as c_1h, sum(c_1i) as c_1i , sum(c_1j) as c_1j, sum(c_1k) as c_1k, sum(c_1l) as c_1l, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_7) as c_7, sum(c_pdrb) as c_pdrb')];

        $this->select_7pkrt =  ['kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a + C_1b) as c_1a, sum(c_1c) as c_1b, sum(c_1d + c_1e) as c_1c, sum(c_1f + c_1j) as c_1d, sum(c_1g + c_1h + c_1i) as c_1e, sum(c_1k) as c_1f, sum(c_1l) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_7) as c_7, sum(c_pdrb) as c_pdrb')];

        $this->select_rilis = ['kode_kab', DB::raw('sum(c_1) as c_1 , sum(c_3) as c_2, sum(c_4) as c_3, sum(c_6 - c_7 + c_5 + c_2) as c_4, sum(c_pdrb) as c_pdrb')];
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
        ['column' => "c_3", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4, c_4a, c_4b", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_5", 'name' => '5. Perubahan Inventori'],
        ['column' => "c_6", 'name' => '6. Ekspor Luar Negeri'],
        ['column' => "c_7", 'name' => '7. Impor Luar Negeri'],
        // ['column' => "c_8, c_8a, c_8b", 'name' => '8. Net Ekspor Antar Daerah'],
        ['column' => "c_pdrb", 'name' => '8 PDRB'],
    ];

    public $list_group_7_pkrt = [
        ['column' => "c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4, c_4a, c_4b", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_5", 'name' => '5. Perubahan Inventori'],
        ['column' => "c_6", 'name' => '6. Ekspor Luar Negeri'],
        ['column' => "c_7", 'name' => '7. Impor Luar Negeri'],
        // ['column' => "c_8, c_8a, c_8b", 'name' => '8. Net Ekspor Antar Daerah'],
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
        // ['id' => 'c_3a',  'alias' => '3a.PKP-Kol',          'name' => '3.a. Konsumsi Kolektif'],
        // ['id' => 'c_3b',  'alias' => '3b.PKP-Ind',          'name' => '3.b. Konsumsi Individu'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_4a',  'alias' => '4a. PMTB-Bang',       'name' => '4.a. Bangunan'],
        ['id' => 'c_4b',  'alias' => '4b. PMTB-NB',         'name' => '4.b. Non Bangunan'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        // ['id' => 'c_6a',  'alias' => '6a. XB LN',           'name' => '6.a. Ekspor Barang'],
        // ['id' => 'c_6b',  'alias' => '6b. XJ LN',           'name' => '6.b. Ekspor Jasa'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        // ['id' => 'c_7a',  'alias' => '7a. MB LN',           'name' => '7.a. Impor Barang'],
        // ['id' => 'c_7b',  'alias' => '7b. MJ LN',           'name' => '7.b. Impor Jasa'],
        // ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '8. Net Ekspor Antar Daerah'],
        // ['id' => 'c_8a',  'alias' => '8a. X AP',            'name' => '8.a. Ekspor Antar Daerah'],
        // ['id' => 'c_8b',  'alias' => '8b. M AP',            'name' => '8.b. Impor Antar Daerah']
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
        // ['id' => 'c_3a',  'alias' => '3a.PKP-Kol',          'name' =>  '  3.a. Konsumsi Kolektif'],
        // ['id' => 'c_3b',  'alias' => '3b.PKP-Ind',          'name' =>  '  3.b. Konsumsi Individu'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_4a',  'alias' => '4a. PMTB-Bang',       'name' =>  '  4.a. Bangunan'],
        ['id' => 'c_4b',  'alias' => '4b. PMTB-NB',         'name' =>  '  4.b. Non Bangunan'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        // ['id' => 'c_6a',  'alias' => '6a. XB LN',           'name' =>  '  6.a. Ekspor Barang'],
        // ['id' => 'c_6b',  'alias' => '6b. XJ LN',           'name' =>  '  6.b. Ekspor Jasa'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        // ['id' => 'c_7a',  'alias' => '7a. MB LN',           'name' =>  '  7.a. Impor Barang'],
        // ['id' => 'c_7b',  'alias' => '7b. MJ LN',           'name' =>  '  7.b. Impor Jasa'],
        // ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '  8. Net Ekspor Antar Daerah'],
        // ['id' => 'c_8a',  'alias' => '8a. X AP',            'name' =>  '  8.a. Ekspor Antar Daerah'],
        // ['id' => 'c_8b',  'alias' => '8b. M AP',            'name' =>  '  8.b. Impor Antar Daerah']
    ];

    public $list_detail_komponen_brs = [
        ['id' => 'c_1',   'alias' => '1. PKRT',             'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['id' => 'c_2',   'alias' => '2. PKLNPRT',          'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['id' => 'c_3',   'alias' => '3.PKP',               'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['id' => 'c_4',   'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
        ['id' => 'c_5',   'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
        ['id' => 'c_6',   'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
        ['id' => 'c_7',   'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        // ['id' => 'c_8',   'alias' => '8. Net Ekspor',       'name' => '  8. Net Ekspor Antar Daerah'],
        ['id' => 'c_pdrb', 'alias' => 'PDRB',               'name' =>  'PDRB'],
    ];

    public $list_detail_komponen_rilis = [
        ['id' => 'c_1',     'alias' => '1. Kon Non Publik',  'name' => '1. Konsumsi Akhir Non Publik'],
        ['id' => 'c_2',     'alias' => '2. Kon Publik',      'name' => '2. Konsumsi Akhir Publik'],
        ['id' => 'c_3',     'alias' => '3.Inves',            'name' => '3. Investasi'],
        ['id' => 'c_4',     'alias' => '4. Lainnya',         'name' => '4. Lainnya'],
        ['id' => 'c_pdrb',  'alias' => 'PDRB',               'name' => 'PDRB'],
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

    public function rumus($id, $wilayah_filter, $periode_filter, $komponens, $select)
    {
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
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 1, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, 1, $rev_y, $select);
                    }
                    $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                } else if ($id === '3.2') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_y, $select);
                    }
                    $row[$periode] = $pdrb_y && isset($pdrb_y->$komp_id) ? $pdrb_y->$komp_id : null;
                } else if ($id === '3.3') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                        $pdrb_q_1 = $arr_periode[1] == 1
                            ? $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1, $select)
                            : $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_y, $select);
                        $pdrb_q_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1, $rev_y_1, $select);
                    }
                    $row[$periode] = $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->$komp_id) && $pdrb_q_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / $pdrb_q_1->$komp_id * 100 : null;
                } else if ($id === '3.4') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                        $pdrb_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_y, $select);
                        $pdrb_y_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1, $rev_y_1, $select);
                    }
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
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
                    $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                    $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                    $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, 1, $rev_y, $select);
                    $pdrb_y_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, 1, $rev_y_1, $select);
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->$komp_id) && $pdrb_y_1->$komp_id != 0 ? ($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / $pdrb_y_1->$komp_id * 100 : null;
                } else if ($id === '3.6') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                        $pdrb_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                    } else {
                        $rev_hb = $this->get_rev($wilayah_filter, $arr_periode[0], null, 1, 1);
                        $rev_hk = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $pdrb_hb = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, 1, $rev_hb, $select);
                        $pdrb_hk = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_hk, $select);
                    }
                    $row[$periode] = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0 ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100 : null;
                } else if ($id === '3.7') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_hb = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 1, 1, $select);
                        $pdrb_hk = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                        $pdrb_hb_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 1, 1, $select);
                        $pdrb_hk_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);
                    } else {
                        $rev_hb = $this->get_rev($wilayah_filter, $arr_periode[0], null, 1, 1);
                        $rev_hk = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $rev_hb_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 1, 1);
                        $rev_hk_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);

                        $pdrb_hb = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 1, 1, $rev_hb, $select);
                        $pdrb_hk = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_hk, $select);
                        $pdrb_hb_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 1, 1, $rev_hb_1, $select);
                        $pdrb_hk_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1, $rev_hk_1, $select);
                    }
                    $implisit_y = $pdrb_hb && $pdrb_hk && isset($pdrb_hk->$komp_id) && $pdrb_hk->$komp_id != 0
                        ? $pdrb_hb->$komp_id / $pdrb_hk->$komp_id * 100
                        : null;
                    $implisit_y_1 = $pdrb_hb_1 && $pdrb_hk_1  && isset($pdrb_hk_1->$komp_id) && $pdrb_hk_1->$komp_id != 0
                        ? $pdrb_hb_1->$komp_id / $pdrb_hk_1->$komp_id * 100
                        : null;
                    $row[$periode] = $implisit_y && $implisit_y_1  && $implisit_y_1 != 0 ? ($implisit_y - $implisit_y_1) / $implisit_y_1 * 100 : null;
                } else if ($id === '3.8') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                        $pdrb_q_1 = $arr_periode[1] == 1
                            ? $this->get_data($wilayah_filter, $arr_periode[0] - 1, 4, 2, 1, $select)
                            : $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1] - 1, 2, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_y, $select);
                        $pdrb_q_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1, $rev_y_1, $select);
                    }
                    $laju_pertumbuhan =  $pdrb_y && $pdrb_q_1 && isset($pdrb_q_1->c_pdrb) && $pdrb_q_1->c_pdrb != 0
                        ? ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) / $pdrb_q_1->c_pdrb * 100
                        : null;
                    $row[$periode] = $pdrb_y && $pdrb_q_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_q_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_q_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_q_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_q_1->c_pdrb)) * $laju_pertumbuhan
                        : null;
                } else if ($id === '3.9') {
                    if (sizeof($arr_periode) > 1) {
                        $pdrb_y = $this->get_data($wilayah_filter, $arr_periode[0], $arr_periode[1], 2, 1, $select);
                        $pdrb_y_1 = $this->get_data($wilayah_filter, $arr_periode[0] - 1, $arr_periode[1], 2, 1, $select);
                    } else {
                        $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                        $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                        $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], [1, 2, 3, 4], 2, 1, $rev_y, $select);
                        $pdrb_y_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, [1, 2, 3, 4], 2, 1, $rev_y_1, $select);
                    }
                    $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0
                        ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100
                        : null;
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan
                        : null;
                } else if ($id === '3.10') {
                    $q = [];
                    if (sizeof($arr_periode) > 1) {
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                    } else {
                        $q = [1, 2, 3, 4];
                    }
                    $rev_y = $this->get_rev($wilayah_filter, $arr_periode[0], null, 2, 1);
                    $rev_y_1 = $this->get_rev($wilayah_filter, $arr_periode[0] - 1, null, 2, 1);
                    $pdrb_y = $this->get_data_cumulative($wilayah_filter, $arr_periode[0], $q, 2, 1, $rev_y, $select);
                    $pdrb_y_1 = $this->get_data_cumulative($wilayah_filter, $arr_periode[0] - 1, $q, 2, 1, $rev_y_1, $select);

                    $laju_pertumbuhan =  $pdrb_y && $pdrb_y_1 && isset($pdrb_y_1->c_pdrb) && $pdrb_y_1->c_pdrb != 0 ? ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) / $pdrb_y_1->c_pdrb * 100 : null;
                    $row[$periode] = $pdrb_y && $pdrb_y_1 && $laju_pertumbuhan && isset($pdrb_y->$komp_id) && isset($pdrb_y_1->$komp_id)
                        && isset($pdrb_y->c_pdrb) && isset($pdrb_y_1->c_pdrb) && ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb) != 0
                        ? (($pdrb_y->$komp_id - $pdrb_y_1->$komp_id) / ($pdrb_y->c_pdrb - $pdrb_y_1->c_pdrb)) * $laju_pertumbuhan : null;
                }
            }
            $data[] = $row;
        }
        return $data;
    }

    public function kabkot(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_12_pkrt;
        $list_detail_komponen = $this->list_detail_komponen;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_12pkrt;

        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $wilayah_filter = $request->wilayah_filter ? $request->wilayah_filter : '00';
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g, c_1h, c_1i, c_1j, c_1k, c_1l', 'c_2', 'c_3', 'c_4, c_4a, c_4b', 'c_5', 'c_6', 'c_7', 'c_pdrb'];

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
        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponens, $select);
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
        $select = $this->select_7pkrt;

        $periode_filter = $request->periode_filter ? $request->periode_filter : [$tahun_berlaku . 'Q1', $tahun_berlaku . 'Q2', $tahun_berlaku . 'Q3', $tahun_berlaku . 'Q4', $tahun_berlaku];
        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g', 'c_2', 'c_3', 'c_4, c_4a, c_4b', 'c_5', 'c_6', 'c_7', 'c_pdrb'];
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
        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponens, $select);
        return view('pdrb_kabkot.index', compact('list_tabel', 'tahun_berlaku',  'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }

    public function kabkot_rilis(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_periode = $this->list_periode;
        $list_group_komponen = $this->list_group_rilis_komponen;
        $list_detail_komponen = $this->list_detail_komponen_rilis;
        $list_wilayah = $this->list_wilayah;
        $tahun_berlaku = $this->tahun_berlaku;
        $select = $this->select_rilis;

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
        $data = $this->rumus($id, $wilayah_filter, $periode_filter, $komponens, $select);
        return view('pdrb_kabkot.index', compact('list_tabel', 'tahun_berlaku', 'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
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
        return view('pdrb_kabkot.kabkot_brs', compact('list_tabel', 'list_periode', 'list_group_komponen', 'list_wilayah', 'tabel_filter', 'periode_filter', 'komponen_filter', 'wilayah_filter', 'data'));
    }
}
