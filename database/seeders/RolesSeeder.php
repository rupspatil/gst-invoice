<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // create roles
        $admin = Role::firstOrCreate(['name'=>'admin']);
        $staff = Role::firstOrCreate(['name'=>'staff']);

        // create permissions (example)
        Permission::firstOrCreate(['name'=>'manage invoices']);
        Permission::firstOrCreate(['name'=>'manage customers']);

        // assign permissions to roles
        $admin->givePermissionTo(['manage invoices','manage customers']);
        $staff->givePermissionTo(['manage invoices']);

        // create admin user (change email/password)
        $u = User::firstOrCreate(['email'=>'admin@example.com'], [
            'name'=>'Admin',
            'password'=>bcrypt('password')
        ]);
        $u->assignRole($admin);
    }
}

