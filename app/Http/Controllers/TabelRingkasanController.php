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
            'name' => 'Tabel 1.1. Perbandingan Pertumbuhan Ekonomi Nasional dan Regional Menurut Komponen',
            'url' => 'pdrb_ringkasan1'
        ],
        [
            'id' => '1.2',
            'name' => 'Tabel 1.2. Perbandingan Pertumbuhan Implisit Nasional dan Regional Menurut Komponen',
            'url' => 'pdrb_ringkasan1'
        ],
        [
            'id' => '1.3',
            'name' => 'Tabel 1.3. Ringkasan Pertumbuhan Ekonomi 34 Provinsi',
            'url' => 'pdrb_ringkasan2'
        ],
        [
            'id' => '1.4',
            'name' => 'Tabel 1.4. Pertumbuhan Ekonomi (Y-on-Y) Per Komponen/Sub Komponen 34 Provinsi - 2024Q3',
            'url' => 'pdrb_ringkasan3'
        ]
    ];
    public $list_quartil = [
        '2024Q1',
        '2024Q2',
        '2024Q3',
        '2024Q4',
        '2024'
    ];
    public $list_komponen = [
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
        ['id' => 'c_pdrb', 'alias' => 'PDRB',  'name' =>  'PDRB',],
        ['id' => 'c_1',   'alias' => '1. PKRT',  'name' => '1. Pengeluaran Konsumsi Rumah Tangga',],
        ['id' => 'c_1a',  'alias' => '1a. PKRT-Mamin  ',  'name' =>  '  1.a. Makanan dan Minuman, Selain Restoran',],
        ['id' => 'c_1b',  'alias' => '1b. PKRT-Pakaian',   'name' =>  '  1.b. Pakaian, Alas Kaki dan Jasa Perawatannya',],
        ['id' => 'c_1c',  'alias' => '1c. PKRT-Perumahan',   'name' =>  '  1.c. Perumahan dan Perlengkapan Rumahtangga',],
        ['id' => 'c_1d',  'alias' => '1d. PKRT-Kesehatan ',   'name' =>  '  1.d. Kesehatan dan Pendidikan',],
        ['id' => 'c_1e',  'alias' => '1e. PKRT-Tansport',   'name' =>  '  1.e. Transportasi dan Komunikasi',],
        ['id' => 'c_1f',  'alias' => '1f. PKRT-Restoran ',   'name' =>  '  1.f. Restoran dan Hotel',],
        ['id' => 'c_1g',  'alias' => '1g. PKRT-Lainnya',   'name' =>  '  1.g. Lainnya',],
        ['id' => 'c_2',   'alias' => '2. PKLNPRT',   'name' => '2. Pengeluaran Konsumsi LNPRT',],
        ['id' => 'c_3',   'alias' => '3.PKP',   'name' => '3. Pengeluaran Konsumsi Pemerintah',],
        ['id' => 'c_3a',  'alias' => '3a.PKP-Kol',   'name' =>  '  3.a. Konsumsi Kolektif',],
        ['id' => 'c_3b',  'alias' => '3b.PKP-Ind',   'name' =>  '  3.b. Konsumsi Individu',],
        ['id' => 'c_4',   'alias' => '4. PMTB',   'name' => '4. Pembentukan Modal Tetap Bruto',],
        ['id' => 'c_4a',  'alias' => '4a. PMTB-Bang',   'name' =>  '  4.a. Bangunan',],
        ['id' => 'c_4b',  'alias' => '4b. PMTB-NB',   'name' =>  '  4.b. Non Bangunan',],
        ['id' => 'c_5',   'alias' => '5. PI',   'name' => '5. Perubahan Inventori',],
        ['id' => 'c_6',   'alias' => '6. X LN',   'name' => '6. Ekspor Luar Negeri',],
        ['id' => 'c_6a',  'alias' => '6a. XB LN',   'name' =>  '  6.a. Ekspor Barang',],
        ['id' => 'c_6b',  'alias' => '6b. XJ LN',   'name' =>  '  6.b. Ekspor Jasa',],
        ['id' => 'c_7',   'alias' => '7. M LN',   'name' => '7. Impor Luar Negeri',],
        ['id' => 'c_7a',  'alias' => '7a. MB LN',   'name' =>  '  7.a. Impor Barang',],
        ['id' => 'c_7b',  'alias' => '7b. MJ LN',   'name' =>  '  7.b. Impor Jasa',],
        ['id' => 'c_8',   'alias' => '8. Net Ekspor',   'name' => '  8. Net Ekspor Antar Daerah',],
        ['id' => 'c_8a',  'alias' => '8a. X AP',   'name' =>  '  8.a. Ekspor Antar Daerah',],
        ['id' => 'c_8b',  'alias' => '8b. M AP',   'name' =>  '  8.b. Impor Antar Daerah',]
    ];

    public $list_wilayah = [
        ['id' => '00', 'alias' => 'Sumsel', 'name' => 'Sumatera Selatan'],
        ['id' => '01', 'alias' => 'OKU', 'name' => 'Ogan Komering Ulu'],
        ['id' => '02', 'alias' => 'OKI', 'name' => 'Ogan Komering Ulu'],
        ['id' => '03', 'alias' => 'Muara Enim', 'name' => 'Ogan Komering Ulu'],
        ['id' => '04', 'alias' => 'Lahat', 'name' => 'Ogan Komering Ulu'],
        ['id' => '05', 'alias' => 'Musi Rawas', 'name' => 'Ogan Komering Ulu'],
        ['id' => '06', 'alias' => 'Muba', 'name' => 'Ogan Komering Ulu'],
        ['id' => '07', 'alias' => 'Banyu Asin', 'name' => 'Ogan Komering Ulu'],
        ['id' => '08', 'alias' => 'OKUS', 'name' => 'Ogan Komering Ulu'],
        ['id' => '09', 'alias' => 'OKUT', 'name' => 'Ogan Komering Ulu'],
        ['id' => '10', 'alias' => 'Ogan Ilir', 'name' => 'Ogan Komering Ulu'],
        ['id' => '11', 'alias' => 'Empat Lawang', 'name' => 'Ogan Komering Ulu'],
        ['id' => '12', 'alias' => 'PALI', 'name' => 'Ogan Komering Ulu'],
        ['id' => '13', 'alias' => 'Muratara', 'name' => 'Ogan Komering Ulu'],
        ['id' => '71', 'alias' => 'Palembang', 'name' => 'Ogan Komering Ulu'],
        ['id' => '72', 'alias' => 'Prabumulih', 'name' => 'Ogan Komering Ulu'],
        ['id' => '73', 'alias' => 'Pagar Alam', 'name' => 'Ogan Komering Ulu'],
        ['id' => '74', 'alias' => 'Lubuk Linggau', 'name' => 'Ogan Komering Ulu'],
    ];

    public function ringkasan1(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_quartil = $this->list_quartil;
        $list_komponen = $this->list_komponen;
        $list_detail_komponen = $this->list_detail_komponen;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.1';
        $periode_filter = $request->periode_filter ? $request->periode_filter : ['2024Q1', '2024Q2', '2024Q3', '2024Q4', '2024'];
        $komponen_filter = $request->komponen_filter ? $request->komponen_filter : ['c_pdrb', 'c_1, c_1a, c_1b, c_1d, c_1e, c_1f, c_1g', 'c_2', 'c_3, c_3a, c_3b', 'c_4, c_4a, c_4b', 'c_5', 'c_6, c_6a, c_6b', 'c_7, c_7a, c_7b'];
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
            // $isFirstIteration = true;
            $komp_id = $komponen['id'];
            // perulangan periode 2024Q1, 2024Q2, 2024Q3 dst
            foreach ($periode_filter as $periode) {
                // if ($isFirstIteration) {
                //     // Jika iterasi pertama, lewati
                //     $isFirstIteration = false;
                //     continue; // Lompat ke iterasi berikutnya
                // }
                $arr_periode = explode("Q", $periode);
                if (sizeof($arr_periode) > 1) {
                    // jika ada Q, misal 2024Q1
                    if ($id == '1.1') {
                        $data_current = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->first();
                        $data_prevyear = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->first();
                        if ($arr_periode[1] == 1) {
                            $data_prevquartile = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->first();
                        } else {
                            $data_prevquartile = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->where('q', 4)->first();
                        }
                        $q = [];
                        for ($i = 1; $i <= $arr_periode[1]; $i++) {
                            $q[] = $i;
                        }
                        $data_cumulative = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0])->whereIn('q', $q)->get();
                        $data_prevcum = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->whereIn('q', $q)->get();
                        // untuk trace data
                        // if ($data_current) {
                        //     $row[$periode . 'data_current'] = $data_current->$komponen;
                        // }
                        // if ($data_prevyear) {
                        //     $row[$periode . 'data_prevyear'] = $data_prevyear->$komponen;
                        // }
                        // if ($data_prevquartile) {
                        //     $row[$periode . 'data_prevquartile'] = $data_prevquartile->$komponen;
                        // }
                        // if ($data_cumulative) {
                        //     $row[$periode . 'data_cumulative'] = $data_cumulative->sum($komponen);
                        // }
                        // if ($data_prevcum) {
                        //     $row[$periode . 'data_prevcum'] = $data_prevcum->sum($komponen);
                        // }
                        // Y-o-Y
                        if ($data_current && $data_prevyear) {
                            $row[$periode . 'yoy'] = ($data_prevyear->$komp_id - $data_current->$komp_id) / $data_prevyear->$komp_id;
                        } else {
                            $row[$periode . 'yoy'] = null;
                        }
                        // Q -t-Q
                        if ($data_current && $data_prevquartile) {
                            $row[$periode . 'qtq'] = ($data_prevquartile->$komp_id - $data_current->$komp_id) / $data_prevquartile->$komp_id;
                        } else {
                            $row[$periode . 'qtq'] = null;
                        }
                        // C-t-C
                        if ($data_cumulative && $data_prevcum) {
                            $row[$periode . 'ctc'] = ($data_prevcum->sum($komp_id) - $data_cumulative->sum($komp_id)) / $data_prevcum->sum($komp_id);
                        } else {
                            $row[$periode . 'ctc'] = null;
                        }
                    } else if ($id == '1.2') {
                    }
                } else {
                    // Jika satu tahun penuh, misal 2024
                    if ($id == '1.1') {
                        $data_current = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0])->get();
                        $data_prevyear = Pdrb::where('kode_kab', '00')->where('tahun', $arr_periode[0] - 1)->get();
                        // untuk trace data
                        // if ($data_current) {
                        //     $row[$periode . 'data_current'] = $data_current->$komponen;
                        // }
                        // if ($data_prevyear) {
                        //     $row[$periode . 'data_prevyear'] = $data_prevyear->$komponen;
                        // }
                        if ($data_current && $data_prevyear) {
                            $row[$periode . 'yoy'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                            $row[$periode . 'qtq'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                            $row[$periode . 'ctc'] = ($data_prevyear->sum($komp_id) - $data_current->sum($komp_id)) / $data_prevyear->sum($komp_id);
                        } else {
                            $row[$periode . 'yoy'] = null;
                            $row[$periode . 'qtq'] = null;
                            $row[$periode . 'ctc'] = null;
                        }
                    } else if ($id == '1.2') {
                    }
                }
            }
            $data[] = $row;
        }
        return view('pdrb_ringkasan.ringkasan1', compact('list_tabel', 'list_quartil', 'list_komponen', 'tabel_filter', 'periode_filter', 'komponen_filter', 'data'));
    }

    public function ringkasan2(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_quartil = $this->list_quartil;
        $list_wilayah = $this->list_wilayah;

        $tabel_filter = $request->tabel_filter ? $request->tabel_filter : '1.3';
        $periode_filter = $request->periode_filter ? $request->periode_filter : '2024Q2';

        $data = [];

        foreach ($list_wilayah as $wilayah) {
            $row = [];
            $arr_periode = explode("Q", $periode_filter);
            if (sizeof($arr_periode) > 1) {
                $data_y = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_y_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $data_y_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 2)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();

                if ($arr_periode[1] == 1) {
                    $data_q_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0])->where('q', $arr_periode[1] - 1)->orderBy('revisi_ke', 'desc')->first();
                    $data_q_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1] - 1)->orderBy('revisi_ke', 'desc')->first();
                } else {
                    $data_q_1 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 1)->where('q', 4)->orderBy('revisi_ke', 'desc')->first();
                    $data_q_2 = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 2)->where('q', 4)->orderBy('revisi_ke', 'desc')->first();
                }
                $q = [];
                for ($i = 1; $i <= $arr_periode[1]; $i++) {
                    $q[] = $i;
                }

                $jml_q_y =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_1 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $jml_q_y_2 =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 2)
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();



                $data_cum_y = Pdrb::select('kode_kab', DB::raw('sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])
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
                    ->where('kode_kab', $wilayah['id'])
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
                    ->where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q_y_2) {
                        foreach ($jml_q_y_2 as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $row['wilayah'] = [
                    'id' => $wilayah['id'],
                    'name' => $wilayah['name'],
                    'alias' => $wilayah['alias']
                ];

                if ($data_y && $data_y_1) {
                    $row['data']['yoy_current'] = round(($data_y->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb, 2);
                } else {
                    $row['data']['yoy_current'] = "";
                }
                if ($data_y_1 && $data_y_2) {
                    $row['data']['yoy_prev'] =
                        round(($data_y_2->c_pdrb - $data_y_1->c_pdrb) / $data_y_1->c_pdrb, 2);
                } else {
                    $row['data']['yoy_prev'] = "";
                }
                if ($data_y && $data_q_1) {
                    $row['data']['qtq_current'] =
                        round(($data_y->c_pdrb - $data_q_1->c_pdrb) / $data_q_1->c_pdrb, 2);
                } else {
                    $row['data']['qtq_current'] = "";
                }
                if ($data_y_1 && $data_q_2) {
                    $row['data']['qtq_prev'] =
                        round(($data_y_1->c_pdrb - $data_q_2->c_pdrb) / $data_q_2->c_pdrb, 2);
                } else {
                    $row['data']['qtq_prev'] = "";
                }
                if ($data_cum_y && $data_cum_y_1) {
                    $row['data']['ctc_current'] =
                        round(($data_cum_y_1->c_pdrb - $data_cum_y->c_pdrb) / $data_cum_y_1->c_pdrb, 2);
                } else {
                    $row['data']['ctc_current'] = "";
                }
                if ($data_cum_y_1 && $data_cum_y_2) {
                    $row['data']['ctc_prev'] =
                        round(($data_cum_y_2->c_pdrb - $data_cum_y_1->c_pdrb) / $data_cum_y_1->c_pdrb, 2);
                } else {
                    $row['data']['ctc_prev'] = "";
                }
                $data[] = $row;
            } else {
            }
        }
        // dd($data);
        return view('pdrb_ringkasan.ringkasan2', compact('list_tabel', 'list_quartil', 'periode_filter', 'tabel_filter', 'data'));
    }

    public function ringkasan3(Request $request, $id)
    {
        $list_tabel = $this->list_tabel;
        $list_quartil = $this->list_quartil;
        $list_komponen = $this->list_komponen;
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
            $arr_periode = explode("Q", $periode_filter);
            if (sizeof($arr_periode) > 1) {
                $pdrb_current = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0])->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                $pdrb_prev = Pdrb::where('kode_kab', $wilayah['id'])->where('tahun', $arr_periode[0] - 1)->where('q', $arr_periode[1])->orderBy('revisi_ke', 'desc')->first();
                if ($pdrb_current && $pdrb_prev) {
                    foreach ($komponen_filter as $komp) {
                        $data_komponen[$komp] = ($pdrb_current->$komp - $pdrb_prev->$komp) / $pdrb_prev->$komp;
                    }
                    $row = [
                        'kode' => $komponen_filter,
                        'data' => $data_komponen,
                        'wilayah' => [
                            'name' => $wilayah['name'],
                            'alias' => $wilayah['alias']
                        ]
                    ];
                    $data[] = $row;
                }
            } else {

                $jml_q =  Pdrb::where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])
                    ->selectRaw('q, MAX(revisi_ke) as max_revisi')
                    ->groupBy('q')
                    ->get();

                $pdrb_current = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0])
                    ->where(function ($query) use ($jml_q) {
                        foreach ($jml_q as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                $pdrb_prev = Pdrb::select('kode_kab', DB::raw('sum(c_1) as c_1, sum(c_1a) as c_1a, sum(c_1b) as c_1b,sum(c_1c) as c_1c, sum(c_1d) as c_1d, sum(c_1e) as c_1e, sum(c_1f) as c_1f, sum(c_1g) as c_1g, sum(c_2) as c_2, sum(c_3) as c_3, sum(c_3a) as c_3a, sum(c_3b) as c_3b, sum(c_4) c_4, sum(c_4a) c_4a, sum(c_4b) c_4b, sum(c_5) as c_5, sum(c_6) as c_6, sum(c_6a) c_6a, sum(c_6b) as c_6b, sum(c_7) as c_7, sum(c_7a) as c_7a, sum(c_7b) as c_7b, sum(c_8) as c_8 , sum(c_8a) as c_8a, sum(c_8b) as c_8b, sum(c_pdrb) as c_pdrb'))
                    ->where('kode_kab', $wilayah['id'])
                    ->where('tahun', $arr_periode[0] - 1)
                    ->where(function ($query) use ($jml_q) {
                        foreach ($jml_q as $q) {
                            $query->orWhere(function ($subquery) use ($q) {
                                $subquery->where('q', $q->q)
                                    ->where('revisi_ke', $q->max_revisi);
                            });
                        }
                    })->groupBy('kode_kab')->first();

                if ($pdrb_current && $pdrb_prev) {
                    foreach ($komponen_filter as $komp) {
                        $data_komponen[$komp] = ($pdrb_current->$komp - $pdrb_prev->$komp) / $pdrb_prev->$komp;
                    }
                    $row = [
                        'kode' => $komponen_filter,
                        'data' => $data_komponen,
                        'wilayah' => [
                            'name' => $wilayah['name'],
                            'alias' => $wilayah['alias']
                        ]
                    ];
                    $data[] = $row;
                }
            }
        }
        return view('pdrb_ringkasan.ringkasan3', compact('list_tabel', 'list_quartil', 'list_detail_komponen', 'komponen_filter', 'komponens', 'tabel_filter', 'periode_filter', 'data'));
    }
}
