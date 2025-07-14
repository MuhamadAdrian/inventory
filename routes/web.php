<?php

use App\Http\Controllers\Admin\BusinessLocation\BusinessLocationController;
use App\Http\Controllers\Admin\Stock\StockController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\ProductStoreController;
use App\Http\Controllers\Admin\Stock\StockHistoryController;
use App\Http\Controllers\Admin\Stock\StockInController;
use App\Http\Controllers\Admin\Stock\StockOutController;
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

    Route::controller(ProductStoreController::class)
        ->prefix('sotre')
        ->name('store.')
        ->group(function() {
            Route::get('data', 'data')->name('data');
        });
    Route::resource('store', ProductStoreController::class);
});
Route::resource('products', ProductController::class)->except(['show']);

// Stock
Route::controller(StockController::class)->prefix('stock-out-requests')->name('stock-out-requests.')->group(function() {
    Route::get('products', 'getProductsForSelectionData')->name('products_for_selection_data');
    Route::put('process-stock-out/{stock_out_request}', 'processTransfer')->name('process');
    Route::get('data', 'data')->name('data');
    Route::put('cancel/{stock_out_request}', 'cancelTransfer')->name('cancel');
    Route::post('{stock_out_request}/print', 'printStockOutRequest')->name('print');
    Route::post('{stock_out_request}/send', 'sendStockOutRequest')->name('send');
    Route::get('{stock_out_request}/scan/{item_id}', 'stockInConfirmationPage')->name('stock_in_confirmation_page');
    Route::post('{stock_out_request_item}/stock-in-store', 'stockInStoreProceed')->name('stock-in-store-proceed');
});
Route::resource('stock-out-requests', StockController::class)->except(['edit', 'update', 'destroy']);

Route::resource('stock-in', StockInController::class);

Route::resource('stock-out', StockOutController::class);

Route::controller(StockHistoryController::class)
    ->prefix('stock-history')
    ->name('stock-history.')
    ->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('{stock-history}', 'show')->name('show');
        Route::get('/data', 'data')->name('data');
    });


// Location Management
Route::controller(BusinessLocationController::class)
    ->prefix('business-locations')
    ->name('business-locations.')
    ->group(function() {
        Route::get('data', 'data')->name('data');
    });
Route::resource('business-locations', BusinessLocationController::class)->except(['show']);
