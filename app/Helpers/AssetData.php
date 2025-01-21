<?php
namespace App\Helpers;

class AssetData{

    public static function getDetailKomponen(){
        return [
            ['id' => 'c_pdrb',  'select_id' => 'c_pdrb',            'alias' => 'PDRB',                'name' => 'PDRB'],
            ['id' => 'c_1',     'select_id' => 'c_1',               'alias' => '1. PKRT',             'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
            ['id' => 'c_1a',    'select_id' => 'c_1a + c_1b',       'alias' => '1a. PKRT-Mamin  ',    'name' => '1.a. Makanan, Minuman dan Rokok '],
            ['id' => 'c_1b',    'select_id' => 'c_1c',              'alias' => '1b. PKRT-Pakaian',    'name' => '1.b. Pakaian dan Alas Kaki'],
            ['id' => 'c_1c',    'select_id' => 'c_1d + c_1e',       'alias' => '1c. PKRT-Perumahan',  'name' => '1.c. Perumahan, Perkakas, Perlengkapan dan Penyelenggaraan Rumah Tangga'],
            ['id' => 'c_1d',    'select_id' => 'c_1f + c_1j',       'alias' => '1d. PKRT-Kesehatan ', 'name' => '1.d. Kesehatan dan Pendidikan'],
            ['id' => 'c_1e',    'select_id' => 'c_1g + c_1h + c_1i','alias' => '1e. PKRT-Tansport',   'name' => '1.e. Trasportasi, Komunikasi, Rekreasi dan Budaya'],
            ['id' => 'c_1f',    'select_id' => 'c_1k',              'alias' => '1f. PKRT-Restoran ',  'name' => '1.f. Hotel dan Restoran'],
            ['id' => 'c_1g',    'select_id' => 'c_1l',              'alias' => '1g. PKRT-Lainnya',    'name' => '1.g. Lainnya'],
            ['id' => 'c_2',     'select_id' => 'c_2',               'alias' => '2. PKLNPRT',          'name' => '2. Pengeluaran Konsumsi LNPRT'],
            ['id' => 'c_3',     'select_id' => 'c_3',               'alias' => '3.PKP',               'name' => '3. Pengeluaran Konsumsi Pemerintah'],
            ['id' => 'c_4',     'select_id' => 'c_4',               'alias' => '4. PMTB',             'name' => '4. Pembentukan Modal Tetap Bruto'],
            ['id' => 'c_4a',    'select_id' => 'c_4a',              'alias' => '4a. PMTB-Bang',       'name' => '4.a. Bangunan'],
            ['id' => 'c_4b',    'select_id' => 'c_4b',              'alias' => '4b. PMTB-NB',         'name' => '4.b. Non Bangunan'],
            ['id' => 'c_5',     'select_id' => 'c_5',               'alias' => '5. PI',               'name' => '5. Perubahan Inventori'],
            ['id' => 'c_6',     'select_id' => 'c_6',               'alias' => '6. X LN',             'name' => '6. Ekspor Luar Negeri'],
            ['id' => 'c_7',     'select_id' => 'c_7',               'alias' => '7. M LN',             'name' => '7. Impor Luar Negeri'],
        ];
    }

    public static function getGroupKomponen(){
        return [
            ['column' => 'c_pdrb',                                          'name' => 'PDRB'],
            ['column' => 'c_1, c_1a, c_1b, c_1c, c_1d, c_1e, c_1f, c_1g',   'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
            ['column' => 'c_2',                                             'name' => '2. Pengeluaran Konsumsi LNPRT'],
            ['column' => 'c_3',                                             'name' => '3. Pengeluaran Konsumsi Pemerintah'],
            ['column' => 'c_4, c_4a, c_4b',                                 'name' => '4. Pembentukan Modal tetap Bruto'],
            ['column' => 'c_5',                                             'name' => '5. Perubahan Inventori'],
            ['column' => 'c_6',                                             'name' => '6. Ekspor Luar Negeri'],
            ['column' => 'c_7',                                             'name' => '7. Impor Luar Negeri']
        ];
    }

    public static $list_group_12_pkrt = [
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

    public static $list_detail_komponen_12_pkrt = [
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

    public static $list_group_rilis_komponen = [
        ['column' => "c_1", 'name' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['column' => "c_2", 'name' => '2. Pengeluaran Konsumsi LNPRT'],
        ['column' => "c_3", 'name' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['column' => "c_4", 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['column' => "c_pdrb", 'name' => '5. PDRB'],
    ];
    
    public static $list_detail_komponen_rilis = [
        ['id' => 'c_1',   'select_id' => 'c_1',    'alias' => '1. Kon Non Publik',  'name' => '1. Konsumsi Akhir Non Publik', 'name_group' => '1. Pengeluaran Konsumsi Rumah Tangga'],
        ['id' => 'c_2',   'select_id' => 'c_3',    'alias' => '2. Kon Publik',      'name' => '2. Konsumsi Akhir Publik', 'name_group' => '2. Pengeluaran Konsumsi LNPRT'],
        ['id' => 'c_3',   'select_id' => 'c_4',    'alias' => '3.Inves',            'name' => '3. Investasi', 'name_group' => '3. Pengeluaran Konsumsi Pemerintah'],
        ['id' => 'c_4',    'select_id' => 'c_6 - c_7 + c_5 + c_2',   'alias' => '4. Lainnya', 'name_group' => '4. Lainnya', 'name' => '4. Pembentukan Modal tetap Bruto'],
        ['id' => 'c_pdrb', 'select_id' => 'c_pdrb',   'alias' => 'PDRB',  'name' => 'PDRB', 'name_group' => '5. PDRB'],
    ];
}
