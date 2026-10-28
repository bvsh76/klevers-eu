<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SegmentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'show'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('pages', PageController::class)->except('show')->parameters(['pages' => 'id']);
        Route::resource('segments', SegmentController::class)->except('show')->parameters(['segments' => 'id']);
        Route::resource('services', ServiceController::class)->except('show')->parameters(['services' => 'id']);
        Route::resource('packages', PackageController::class)->except('show')->parameters(['packages' => 'id']);
        Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
        Route::resource('leads', LeadController::class)->only(['index', 'show', 'update'])->parameters(['leads' => 'id']);
        Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
        Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
