<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('000000')
        ]);

        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        DB::table('petugas')->insert([
            'nip' => '111',
            'user_id' => $admin->id,
        ]);
   
        $role->syncPermissions([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,29,30,31,32]);

        $admin->assignRole([$role->id]);

        // User Petugas
        $role_petugas = Role::create([
            'name' => 'petugas',
            'guard_name' => 'web'
        ]);
   
        $role_petugas->syncPermissions([23,24]);

        // User mahasiswa
        $role_mhs = Role::create([
            'name' => 'mahasiswa',
            'guard_name' => 'web'
        ]);
   
        $role_mhs->syncPermissions([25,26,27,28]);
    }
}
