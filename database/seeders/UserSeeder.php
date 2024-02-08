<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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
            'login_key' => 'admin@gmail.com',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('000000')
        ]);

        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        DB::table('profile_internals')->insert([
            'nip' => '111',
            'user_id' => $admin->id,
            'tempat_lahir' => 'depok',
            'tgl_lahir' => '2022-01-01',
            'no_telp' => '0',
            'jk' => 'l',
            'riwayat_pendidikan' => '1',
            'alamat' => '1',
            'status' => '1'
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
