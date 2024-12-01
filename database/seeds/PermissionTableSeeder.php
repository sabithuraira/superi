<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //add superadmin role
        $role = Role::create(['name' => 'superadmin']);
        $role2 = Role::create(['name' => 'pemantau']);
        //add permission for all page
        $arr = ['import_pdrb', 'import_fenomena', 'tabel_ringkasan', 'tabel_resume', 
            'tabel_kabkot', 'tabel_history', 'arah_revisi_total', 'arah_revisi_kabkota', 'fenomena_total', 'fenomena_kabkota'];

        foreach($arr as $value){
            Permission::create(['name' => $value]);
        }
        
        $role->syncPermissions($arr);
        $role2->syncPermissions(array_slice($arr,2));
    }
}
