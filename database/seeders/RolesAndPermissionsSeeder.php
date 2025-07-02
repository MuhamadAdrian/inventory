<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions (you can expand this list)
        $permissions = [
            'create user', 'edit user', 'delete user', 'view user',
            'create role', 'edit role', 'delete role', 'view role',
            'assign permissions to roles',
            'create product', 'edit product', 'delete product', 'view product',
            'stock monitoring',
            'scan barcode', 'print barcode',
            'view stock report', 'print stock report',
            'nota assignment', 'nota history', 'nota print'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Define Roles and Assign Permissions
        $ownerRole = Role::findOrCreate('owner');
        $ownerRole->givePermissionTo(Permission::all()); // Owner has all permissions

        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo([
            'create user', 'edit user', 'delete user', 'view user',
            'create product', 'edit product', 'delete product', 'view product',
            'stock monitoring',
            'print barcode',
            'print stock report',
        ]);

        $gudangRole = Role::findOrCreate('gudang');
        $gudangRole->givePermissionTo([
            'view product',
            'create user', 'edit user', 'delete user', 'view user',
            'stock monitoring',
            'scan barcode',
            'view stock report',
            'nota assignment', 'nota history', 'nota print'
        ]);

        $kasirRole = Role::findOrCreate('kasir');
        $kasirRole->givePermissionTo([
            'view product',
            'create user', 'edit user', 'delete user', 'view user'
        ]);

        // Create a default owner user
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner User',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );
        $ownerUser->assignRole('owner');

        // Create a default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Create a default gudang user
        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang1@example.com'],
            [
                'name' => 'Gudang User 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );
        $gudangUser->assignRole('gudang');

        // Create a default kasir user
        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir User',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );
        $kasirUser->assignRole('kasir');
    }
}