<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersInsertAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin Kita',
            
            'email' => 'admin@email.com',
            'password' => '$2y$10$4vXfYQk2v2M0Y/JbCi8jj.qAPxa2.QJz.3w4uaq4tMhYhRe/ZPIuO',

            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
            
            'nip_baru' => '12345678901234567890',
            'urutreog' => '',
            'kdorg' => '',
            'nmorg' => '',
            'nmjab' => '',
            'flagwil' => '',
            'kdprop' => '16',
            'kdkab' => '00',
            'kdkec' => '000',
            'nmwil' => '',
            'kdgol' => '',
            'nmgol' => '',
            'kdstjab' => '',
            'nmstjab' => '',
            'kdesl' => '',
        ]);
    }
}
