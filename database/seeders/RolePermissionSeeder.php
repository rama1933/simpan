<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'knowledge.create',
            'knowledge.read',
            'knowledge.update',
            'knowledge.delete',
            'knowledge.verify',
            'user.manage',
            'role.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Admin role
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create User SKPD role
        $userRole = Role::create(['name' => 'User SKPD']);
        $userRole->givePermissionTo([
            'knowledge.create',
            'knowledge.read',
            'knowledge.update'
        ]);
    }
}
