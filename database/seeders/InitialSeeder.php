<?php

namespace Database\Seeders;

use App\Models\BusinessLocation;
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
            'receive notification', 'receive email notification', 'create account', 'view account', 'edit account', 'delete account',
            'view dashboard', 'create role', 'edit role', 'delete role', 'view role', 'assign permissions to roles',
            'create business location', 'edit business location', 'delete business location', 'view business location',
            'view product', 'create product', 'edit product', 'delete product',
            'view store product', 'view stock request', 'view dashboard',
            'import product', 'export product', 'update stock product', 'print barcode',
            'approval stock request', 'export stock request', 'print stock request',
            'scan barcode in', 'scan barcode out', 'create stock request', 'view product stock history', 'export product stock history', 'direct stock out'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Define Roles and Assign Permissions
        $ownerRole = Role::findOrCreate('owner');
        $ownerRole->givePermissionTo([
            'view dashboard', 'create role', 'edit role', 'delete role', 'view role',
            'assign permissions to roles',
            'create account', 'view account', 'edit account', 'delete account',
            'create business location', 'edit business location', 'delete business location', 'view business location',
            'view product', 'view store product', 'view stock request',
            'view product stock history', 'export product stock history'
        ]);

        $staffRole = Role::findOrCreate('staff');
        $staffRole->givePermissionTo([
            'view dashboard', 'import product', 'export product',
            'create product', 'edit product', 'delete product', 'view product',
            'create account', 'edit account', 'delete account', 'view account',
            'update stock product',
            'print barcode',
            'view stock request',
            'direct stock out',
            'create stock request',
            'approval stock request',
            'export stock request',
            'view product stock history',
            'export product stock history'
        ]);

        $gudangRole = Role::findOrCreate('gudang');
        $gudangRole->givePermissionTo([
            'view product', 'export product', 'update stock product', 'print stock request',
            'create stock request', 'view stock request', 'export stock request', 'view store product',
            'create account', 'edit account', 'delete account', 'view account',
        ]);

        $kasirRole = Role::findOrCreate('kasir');
        $kasirRole->givePermissionTo([
            'view store product',
            'create account', 'edit account', 'delete account', 'view account',
            'scan barcode in', 'scan barcode out'
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
        $gudangProfile = BusinessLocation::create([
            'name' => 'Gudang Bandung',
            'city' => 'Bandung',
            'area' => 'Cibiru',
            'phone' => '08892309239',
            'type' => 'warehouse',
            'code' => 'GP-0001'
        ]);

        $officeProfile = BusinessLocation::create([
            'name' => 'Kantor Bandung',
            'city' => 'Bandung',
            'area' => 'Cibiru',
            'phone' => '08892309239',
            'type' => 'office',
            'code' => 'KP-0001'
        ]);

        $storeProfile = BusinessLocation::create([
            'name' => 'Toko Bandung',
            'city' => 'Bandung',
            'area' => 'Cibiru',
            'phone' => '08892309239',
            'type' => 'store',
            'code' => 'TP-0001'
        ]);

        // Create a default admin user
        $staffUser = User::firstOrCreate(
            ['email' => 'staff1@gmail.com'],
            [
                'name' => 'Staff Name 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'business_location_id' => $officeProfile->id
            ]
        );
        $staffUser2 = User::firstOrCreate(
            ['email' => 'staff2@gmail.com'],
            [
                'name' => 'Staff Name 2',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'business_location_id' => $officeProfile->id
            ]
        );
        $staffUser->assignRole('staff');
        $staffUser2->assignRole('staff');

        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang1@gmail.com'],
            [
                'name' => 'Gudang Name 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'business_location_id' => $gudangProfile->id
            ]
        );

        $gudangUser->assignRole('gudang');

        // Create a default kasir user
        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir1@example.com'],
            [
                'name' => 'Kasir Name 1',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'business_location_id' => $storeProfile->id
            ]
        );
        $kasirUser->assignRole('kasir');
    }
}