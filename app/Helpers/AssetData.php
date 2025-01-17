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
}
