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
        $roles = [
            [
                'role' => 'admin',
                'permission' => array_map('strval', range(1, 57))
            ],
            [
                'role' => 'petugas',
                'permission' => []
            ],
            [
                'role' => 'dosen',
                'permission' => []
            ],
            [
                'role' => 'asdos',
                'permission' => []
            ],
            [
                'role' => 'mahasiswa',
                'permission' => []
            ]
        ];

        foreach ($roles as $role) {
            $roleCreated = Role::create([
                'name' => $role['role'],
                'guard_name' => 'web'
            ]);

            $roleCreated->syncPermissions($role['permission']);
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
