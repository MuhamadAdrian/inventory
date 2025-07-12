<?php

use App\Http\Controllers\Admin\BusinessLocation\BusinessLocationController;
use App\Http\Controllers\Admin\Stock\StockController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\User\PermissionController;
use App\Http\Controllers\Admin\User\RoleController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Warehouse\WarehouseProductController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    // dd(auth()->user()->getAllPermissions()->pluck('name')->toArray());
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
Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function(){
    Route::post('gudang/process-adjust-stock', 'processScanStock')->name('adjust_stock');
    Route::get('gudang/scan-barcode', 'showScanStockForm')->name('scan_barcode');
    Route::post('update-stock', 'updateStock')->name('update_stock');
    Route::get('{product}/print-barcode', 'printBarcode')->name('print_barcode');
});
Route::resource('products', ProductController::class)->except(['show']);

// Stock
Route::controller(StockController::class)->prefix('stock-transfers')->name('stock-transfers.')->group(function() {
    Route::get('products', 'getProductsForSelectionData')->name('products_for_selection_data');
});

Route::get('products-warehouse', [WarehouseProductController::class, 'index'])->name('products-warehouse.index');

Route::controller(StockController::class)->prefix('stock-transfers')->name('stock-transfers.')->group(function(){
    Route::put('process-transfer/{stock_transfer}', 'processTransfer')->name('process_transfer');
    Route::get('data', 'data')->name('data');
    Route::put('cancel/{stock_transfer}', 'cancelTransfer')->name('cancel_transfer');
});
Route::resource('stock-transfers', StockController::class)->except(['edit', 'update', 'destroy']);

// Location Management
Route::controller(BusinessLocationController::class)
    ->prefix('business-locations')
    ->name('business-locations.')
    ->group(function() {
        Route::get('data', 'data')->name('data');
    });
Route::resource('business-locations', BusinessLocationController::class)->except(['show']);
