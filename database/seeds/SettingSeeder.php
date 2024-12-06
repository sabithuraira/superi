<?php

use Illuminate\Database\Seeder;
use App\SettingApp;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingApp::create(['setting_name' => 'tahun_berlaku', 'setting_value' => '2024', 'created_by'=> 1, 'updated_by'=> 1]);
        SettingApp::create(['setting_name' => 'triwulan_berlaku', 'setting_value' => '1', 'created_by'=> 1, 'updated_by'=> 1]);
    }
}
