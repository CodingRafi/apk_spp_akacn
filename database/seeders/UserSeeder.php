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
