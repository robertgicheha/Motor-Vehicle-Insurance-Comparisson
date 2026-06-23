<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::middleware(['auth', 'verified', 'online'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::resource('quotations', QuotationController::class)->except(['edit', 'update']);
    Route::post('/quotations/calculate', [QuotationController::class, 'calculate'])->name('quotations.calculate');

    Route::middleware(['role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

        Route::resource('roles', RoleController::class);

        Route::resource('companies', CompanyController::class);
        Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('/companies/{company}/rates', [CompanyController::class, 'rates'])->name('companies.rates');
        Route::post('/companies/{company}/rates', [CompanyController::class, 'addRate'])->name('companies.rates.add');
        Route::patch('/companies/{company}/rates/{rate}', [CompanyController::class, 'updateRate'])->name('companies.rates.update');
        Route::delete('/companies/{company}/rates/{rate}', [CompanyController::class, 'destroyRate'])->name('companies.rates.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

        Route::get('/audit-logs', [AuditLogController::class, 'indexAudit'])->name('audit.index');
        Route::get('/activity-logs', [AuditLogController::class, 'indexActivity'])->name('audit.activity');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__ . '/auth.php';
