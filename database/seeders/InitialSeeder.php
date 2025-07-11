<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;

class InitialSeeder extends Seeder
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
            'nota assignment', 'nota history', 'nota print',
            'view warehouse product', 'stock request',
            'stock request view',
            'stock input to warehouse',
            'stock request process',
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
            'stock request view',
            'stock input to warehouse',
            'stock request process',
        ]);

        $gudangRole = Role::findOrCreate('gudang');
        $gudangRole->givePermissionTo([
            'view product',
            'view warehouse product',
            'create user', 'edit user', 'delete user', 'view user',
            'stock monitoring',
            'stock request',
            'scan barcode',
            'view stock report',
            'nota assignment', 'nota history', 'nota print',
            'stock request view'
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

        // Create a center gudang user
        $gudangProfile = Warehouse::create([
            'name' => 'Gudang Pusat',
            'location' => 'Bandung',
            'code' => 'GP-0001'
        ]);

        // Create a default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Pusat',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'warehouse_id' => $gudangProfile->id
            ]
        );
        $adminUser->assignRole('admin');

        $gudangProfile2 = Warehouse::create([
            'name' => 'Gudang 1',
            'location' => 'Cimahi',
            'code' => 'G-0001'
        ]);

        $adminUser2 = User::firstOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Admin Gudang 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'warehouse_id' => $gudangProfile2->id
            ]
        );
        $adminUser2->assignRole('admin');

        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang1@example.com'],
            [
                'name' => 'Gudang User 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'warehouse_id' => $gudangProfile2->id
            ]
        );

        $gudangUser2 = User::firstOrCreate(
            ['email' => 'gudang2@example.com'],
            [
                'name' => 'Gudang User 2',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'warehouse_id' => $gudangProfile2->id
            ]
        );

        $gudangUser->assignRole('gudang');
        $gudangUser2->assignRole('gudang');

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