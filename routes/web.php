<?php

use App\Http\Controllers\Admin\PermissionController;
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
Route::resources(['users' => UserController::class]);
Route::resources(['roles' => RoleController::class]);
Route::resources(['permissions' => PermissionController::class]);


