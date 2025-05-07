<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Modules\Admin\App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $admin = $this->adminCreation();
        $this->permissionCreation();
        $role = $this->roleCreation();
        $admin->assignRole($role);
        $role2 = $this->role2Creation();
        $role3 = $this->role3Creation();
        $role4 = $this->role4Creation();
    }

    function adminCreation()
    {
        return $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'phone' => '0123456789',
            'password' => Hash::make('123123'),
            'is_active' => 1,
        ]);
    }

    function permissionCreation()
    {
        $permissions = [
            ['Index-admin', 'Admin', 'Index'],
            ['Create-admin', 'Admin', 'Create'],
            ['Edit-admin', 'Admin', 'Edit'],
            ['Delete-admin', 'Admin', 'Delete'],

            ['Index-role', 'Roles', 'Index'],
            ['Create-role', 'Roles', 'Create'],
            ['Edit-role', 'Roles', 'Edit'],
            ['Delete-role', 'Roles', 'Delete'],

            ['Index-client', 'Client', 'Index'],
            ['Create-client', 'Client', 'Create'],
            ['Edit-client', 'Client', 'Edit'],
            ['Delete-client', 'Client', 'Delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission[0], 'category' => $permission[1], 'guard_name' => 'admin', 'display' => $permission[2]]);
        }
    }

    function roleCreation()
    {
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        return $role;
    }

    function role2Creation()
    {
        $role = Role::create(['name' => 'Client', 'guard_name' => 'admin']);
        return $role;
    }

    function role3Creation()
    {
        $role = Role::create(['name' => 'Service Provider', 'guard_name' => 'admin']);
        return $role;
    }

    function role4Creation()
    {
        $role = Role::create(['name' => 'Shop Owner', 'guard_name' => 'admin']);
        return $role;
    }
}
