<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\PricingAdminController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'loginForm'])->name('home');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [EstimateController::class, 'index'])->name('dashboard');

    Route::get('/estimates/create', [EstimateController::class, 'create'])->name('estimates.create');
    Route::post('/estimates', [EstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates/{estimate}', [EstimateController::class, 'show'])->name('estimates.show');
    Route::get('/estimates/{estimate}/edit', [EstimateController::class, 'edit'])->name('estimates.edit');
    Route::put('/estimates/{estimate}', [EstimateController::class, 'update'])->name('estimates.update');
    Route::delete('/estimates/{estimate}', [EstimateController::class, 'destroy'])->name('estimates.destroy');
    Route::get('/estimates/{estimate}/pdf', [EstimateController::class, 'pdf'])->name('estimates.pdf');

    Route::middleware('permission:manage pricing')->group(function () {
        Route::get('/admin/pricing', [PricingAdminController::class, 'index'])->name('admin.pricing');
        Route::put('/admin/pricing', [PricingAdminController::class, 'update'])->name('admin.pricing.update');
    });

    Route::middleware('permission:manage users')->group(function () {
        // Users
        Route::resource('/admin/users', UserManagementController::class)
            ->except(['show'])
            ->names('admin.users');

        // Roles
        Route::get('/admin/roles', [RolesController::class, 'index'])->name('admin.roles.index');
        Route::post('/admin/roles', [RolesController::class, 'store'])->name('admin.roles.store');
        Route::put('/admin/roles/{role}', [RolesController::class, 'update'])->name('admin.roles.update');
        Route::delete('/admin/roles/{role}', [RolesController::class, 'destroy'])->name('admin.roles.destroy');
    });
});
