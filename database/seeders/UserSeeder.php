<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
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
        $roles = ['admin', 'petugas', 'dosen', 'asdos', 'mahasiswa'];
        $roles = [
            [
                'role' => 'admin',
                'permsision' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55'
            ],
            [
                'role' =>'petugas',
                'permission' => ''
            ]
        ]

        foreach ($roles as $role) {
            $roleCreated = Role::create([
                'name' => $role,
                'guard_name' => 'web'
            ]);

            if ($role == 'admin') {
                $permissions = Permission::pluck('id','id')->all();
            }

            $roleCreated->syncPermissions(isset($permissions) ? $permissions : []);
        }

        // User Admin
        $admin = User::create([
            'name' => 'Admin',
            'login_key' => 'admin@gmail.com',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('000000')
        ]);

        $admin->assignRole('admin');
    }
}
