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
                'permission' => array_map('strval', range(1, 84))
            ],
            [
                'role' => 'petugas',
                'permission' => []
            ],
            [
                'role' => 'dosen',
                'permission' => array_map('strval', range(81, 86))
            ],
            [
                'role' => 'asisten',
                'permission' => array_map('strval', range(81, 86))
            ],
            [
                'role' => 'mahasiswa',
                'permission' => array_map('strval', range(94, 111))
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

        $adminPmb = User::create([
            'name' => 'Admin PMB',
            'login_key' => 'adminpmb@gmail.com',
            'email' => 'adminpmb@gmail.com',
            'password' => bcrypt('SOrwb9jwJnEIAxMjRyiKPSs9peob7r[Wx/U!dleIjlpEc0HRaE')
        ]);

        $adminPmb->assignRole('admin');
    }
}
