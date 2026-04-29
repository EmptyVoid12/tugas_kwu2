<?php

use App\Http\Controllers\MonthlyLaundryReportController;
use App\Http\Controllers\TenantLaundryPrintController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
Route::get('/', [TrackingController::class, 'index'])->name('home');

Route::get('/tracking', [TrackingController::class, 'search'])->name('tracking.search');

Route::get('/tracking/code/{code}', [TrackingController::class, 'showByCode'])
    ->name('tracking.code');

Route::get('/tracking/{tenant}/{id}', [TrackingController::class, 'show'])
    ->whereNumber('tenant')
    ->whereNumber('id')
    ->name('tracking.show');

Route::middleware('auth:tenant')->group(function () {
    Route::get('/tenant/laundries/{laundry}/print', TenantLaundryPrintController::class)
        ->whereNumber('laundry')
        ->name('tenant.laundries.print');

    Route::get('/tenant/reports/monthly', [MonthlyLaundryReportController::class, 'tenant'])
        ->name('tenant.reports.monthly');
});

Route::middleware('auth:superadmin')->group(function () {
    Route::get('/superadmin/tenants/{tenant}/reports/monthly', [MonthlyLaundryReportController::class, 'admin'])
        ->whereNumber('tenant')
        ->name('admin.tenants.reports.monthly');
});
