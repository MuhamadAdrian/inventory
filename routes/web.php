<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('index');
})->name('dashboard')
->middleware('auth');

// User & Role Management Routes
Route::resources([
    'users' => UserController::class,
    'roles' => RoleController::class,
    'permissions' => PermissionController::class
]);

// Product Management
Route::post('products/gudang/process-adjust-stock', [ProductController::class, 'processScanStock'])->name('products.adjust_stock');
Route::get('products/gudang/scan-barcode', [ProductController::class, 'showScanStockForm'])->name('products.scan_barcode'); // Moved this up
Route::resource('products', ProductController::class)->except(['show']);
Route::get('products/{product}/print-barcode', [ProductController::class, 'printBarcode'])->name('products.print_barcode'); // This can stay here or be moved up



