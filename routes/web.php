<?php

use App\Http\Controllers\Api\StickerApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\StickerAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/motor-quote', [PublicController::class, 'motorQuote'])->name('motor.quote');
Route::get('/motor-quote/quotation/{company?}', [PublicController::class, 'quotation'])->name('motor.quotation');

Route::prefix('motor-quote')->name('motor.')->group(function () {
    Route::post('/lookup-quotation', [PublicController::class, 'lookupQuotation'])->name('lookup.quotation');
    Route::post('/lookup-sticker', [PublicController::class, 'lookupSticker'])->name('lookup.sticker');
    Route::get('/search', [PublicController::class, 'globalSearch'])->name('search');
});

Route::prefix('lookup')->name('public.')->group(function () {
    Route::get('/quotation/{number}', [PublicController::class, 'showQuotation'])->name('quotation.show');
    Route::get('/sticker/{number}', [PublicController::class, 'showSticker'])->name('sticker.show');
    Route::get('/quotation/{number}/download', [PublicController::class, 'downloadQuotationPdf'])->name('quotation.download');
    Route::get('/sticker/{number}/download', [PublicController::class, 'downloadStickerPdf'])->name('sticker.download');
    Route::post('/quotation/email', [PublicController::class, 'sendQuotationEmail'])->name('quotation.email');
    Route::post('/sticker/email', [PublicController::class, 'sendStickerEmail'])->name('sticker.email');
});

Route::get('/quotation/{quotation}/download-pdf', [QuotationController::class, 'publicDownloadPdf'])->name('quotation.download.pdf');

Route::prefix('stickers')->name('stickers.')->group(function () {
    Route::get('/generate', [StickerController::class, 'create'])->name('create');
    Route::post('/generate', [StickerController::class, 'store'])->name('store');
    Route::get('/lookup', [StickerController::class, 'lookup'])->name('lookup');
    Route::post('/lookup', [StickerController::class, 'search'])->name('search');
    Route::get('/{sticker}', [StickerController::class, 'show'])->name('show');
    Route::get('/{sticker}/download', [StickerController::class, 'downloadPdf'])->name('download');
    Route::get('/{sticker}/print', [StickerController::class, 'printView'])->name('print');
});

Route::post('/api/sticker/generate', [StickerApiController::class, 'generate']);

Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::middleware(['auth', 'verified', 'online'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::resource('quotations', QuotationController::class)->except(['edit', 'update']);
    Route::post('/quotations/calculate', [QuotationController::class, 'calculate'])->name('quotations.calculate');
    Route::get('/quotations/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.download.pdf');

    Route::middleware(['role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import')->middleware('throttle:3,1');

        Route::resource('roles', RoleController::class);

        Route::resource('companies', CompanyController::class);
        Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('/companies/{company}/rates', [CompanyController::class, 'rates'])->name('companies.rates');
        Route::post('/companies/{company}/rates', [CompanyController::class, 'addRate'])->name('companies.rates.add');
        Route::patch('/companies/{company}/rates/{rate}', [CompanyController::class, 'updateRate'])->name('companies.rates.update');
        Route::delete('/companies/{company}/rates/{rate}', [CompanyController::class, 'destroyRate'])->name('companies.rates.destroy');

        Route::get('/stickers', [StickerAdminController::class, 'index'])->name('stickers.index');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate')->middleware('throttle:10,1');
        Route::post('/reports/stickers', [ReportController::class, 'stickerReport'])->name('reports.stickers')->middleware('throttle:10,1');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/stickers/export-csv', [ReportController::class, 'exportStickersCsv'])->name('reports.stickers.export-csv');

        Route::get('/audit-logs', [AuditLogController::class, 'indexAudit'])->name('audit.index');
        Route::get('/activity-logs', [AuditLogController::class, 'indexActivity'])->name('audit.activity');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('throttle:10,1');
    });
});

require __DIR__ . '/auth.php';
