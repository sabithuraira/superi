<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomponenTabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('komponen')->truncate();
        DB::table('komponen')->insert([
            'no_komponen' => '1.',
            'nama_komponen' => 'Pengeluaran Konsumsi Rumah Tangga (1.a. s/d 1.l.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '2.',
            'nama_komponen' => 'Pengeluaran Konsumsi LNPRT',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '3.',
            'nama_komponen' => 'Pengeluaran Konsumsi Pemerintah (3.a. + 3.b.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '4.',
            'nama_komponen' => 'Pembentukan Modal Tetap Bruto (4.a. + 4.b.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '5.',
            'nama_komponen' => 'Perubahan Inventori',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '6.',
            'nama_komponen' => 'Ekspor Luar Negeri (6.a. + 6.b.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '7.',
            'nama_komponen' => 'Impor Luar Negeri (7.a. + 7.b.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);

        DB::table('komponen')->insert([
            'no_komponen' => '8.',
            'nama_komponen' => 'Net Ekspor Antar Daerah (8.a. - 8.b.)',
            'parent_id' => null, 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);

        //////////////////////////////
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.a.',
            'nama_komponen' => 'Makanan dan Minuman Non Beralkohol',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.b.',
            'nama_komponen' => 'Minuman Beralkohol dan Rokok',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.c.',
            'nama_komponen' => 'Pakaian',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);

        
        DB::table('komponen')->insert([
            'no_komponen' => '1.d.',
            'nama_komponen' => 'Perumahan, Air, Listrik, Gas dan Bahan Bakar Lainnya',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.e.',
            'nama_komponen' => 'Perabot, Peralatan rumahtangga dan Pemeliharaan Rutin Rumah',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);

        
        DB::table('komponen')->insert([
            'no_komponen' => '1.f.',
            'nama_komponen' => 'Kesehatan',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.g.',
            'nama_komponen' => 'Transportasi/Angkutan',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.h.',
            'nama_komponen' => 'Komunikasi',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.i.',
            'nama_komponen' => 'Rekreasi dan Budaya',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.j.',
            'nama_komponen' => 'Pendidikan',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.k.',
            'nama_komponen' => 'Penginapan dan Hotel',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '1.l.',
            'nama_komponen' => 'Barang Pribadi dan Jasa Perorangan',
            'parent_id' => '1.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '3.a.',
            'nama_komponen' => 'Konsumsi Kolektif',
            'parent_id' => '3.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '3.b.',
            'nama_komponen' => 'Konsumsi Individu',
            'parent_id' => '3.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '4.a.',
            'nama_komponen' => 'Bangunan',
            'parent_id' => '4.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '4.b.',
            'nama_komponen' => 'Non Bangunan',
            'parent_id' => '4.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '6.a.',
            'nama_komponen' => 'Barang',
            'parent_id' => '6.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '6.b.',
            'nama_komponen' => 'Jasa',
            'parent_id' => '6.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '7.a.',
            'nama_komponen' => 'Barang',
            'parent_id' => '7.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '7.b.',
            'nama_komponen' => 'Jasa',
            'parent_id' => '7.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '8.a.',
            'nama_komponen' => 'Ekspor',
            'parent_id' => '8.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);
        
        DB::table('komponen')->insert([
            'no_komponen' => '8.b.',
            'nama_komponen' => 'Impor',
            'parent_id' => '8.', 'status_aktif' => 1, 'created_by' => 1, 'updated_by' => 1,
        ]);

    }
}
