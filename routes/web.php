<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Users management
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware(['permission:users.create', 'throttle:15,1']);
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware(['permission:users.edit', 'throttle:15,1']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    });

    // Roles management
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware(['permission:roles.create', 'throttle:15,1']);
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware(['permission:roles.edit', 'throttle:15,1']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');
    });

    // Permissions management
    Route::middleware('permission:permissions.view')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:permissions.create');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:permissions.edit');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:permissions.delete');
    });

    // Activity Logs
    Route::middleware('permission:activities.view')->group(function () {
        Route::get('activities', [ActivityLogController::class, 'index'])->name('activities.index');
    });

    // Menus management
    Route::middleware('permission:menus.view')->group(function () {
        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [MenuController::class, 'store'])->name('menus.store')->middleware('permission:menus.create');
        Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update')->middleware('permission:menus.edit');
        Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy')->middleware('permission:menus.delete');
    });
});

require __DIR__.'/settings.php';
