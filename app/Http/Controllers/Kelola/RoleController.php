<?php
namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view_roles|add_roles|edit_roles|delete_roles', ['only' => ['index', 'store']]);
        $this->middleware('permission:edit_roles', ['only' => ['edit', 'update']]);
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        $rolePermissions = [];

        foreach ($roles as $role) {
            $rolePermissions[] = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $role->id)
                ->get();
        }

        $permissions = Permission::get();
        return view('roles.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.update', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permission);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }
}
