<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\VehicleDataController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\StickerAdminController;
use App\Http\Controllers\Admin\StickerSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\CompareAllController;
use App\Http\Controllers\Admin\RateSettingController;
use App\Http\Controllers\Admin\HealthCheckController;
use App\Http\Controllers\Admin\ChangeRequestController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\User\HelpController as UserHelpController;
use App\Http\Controllers\ClaimsController;
use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/compare-all', fn () => redirect()->route('generate.quote', 'compare-all'))->name('compare-all');

Route::get('/motor-quote', [PublicController::class, 'motorQuote'])->name('motor.quote');
Route::get('/motor-private', [PublicController::class, 'motorPrivate'])->name('motor.private');
Route::get('/motor-psv', [PublicController::class, 'motorPsv'])->name('motor.psv');
Route::get('/motor-quote/stats', [PublicController::class, 'statsJson'])->name('motor.stats');
Route::get('/generate-quote/{company?}', [PublicController::class, 'generateQuote'])->name('generate.quote');
Route::get('/quotation/{company?}', [PublicController::class, 'quotation'])->name('motor.quotation');
Route::get('/psv-quote', [PublicController::class, 'motorQuote'])->name('psv.quote');
Route::get('/private-quote', [PublicController::class, 'motorQuote'])->name('private.quote');
Route::get('/psv-insurance/{company}', [PublicController::class, 'psvInsurance'])->name('psv.insurance');
Route::get('/private-insurance/{company}', [PublicController::class, 'privateInsurance'])->name('private.insurance');

Route::get('/api/vehicle-makes', [VehicleDataController::class, 'makes']);
Route::get('/api/vehicle-models/{make}', [VehicleDataController::class, 'models']);

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

Route::post('/quotations/regenerate', [QuotationController::class, 'regenerate'])->name('quotations.regenerate');

Route::prefix('stickers')->name('stickers.')->group(function () {
    Route::get('/user-lookup', [StickerController::class, 'userLookup'])->name('user-lookup');
    Route::post('/lookup-by-quotation', [StickerController::class, 'lookupByQuotation'])->name('lookup-by-quotation');
    Route::get('/issue', [StickerController::class, 'issueForm'])->name('issue');
    Route::post('/search-quotation', [StickerController::class, 'searchQuotation'])->name('search-quotation');
    Route::post('/upload-document', [StickerController::class, 'uploadDocument'])->name('upload-document');
    Route::post('/scan-status', [StickerController::class, 'scanStatus'])->name('scan-status');
    Route::post('/issue', [StickerController::class, 'issue'])->name('issue.store');
    Route::post('/test-email', [StickerController::class, 'sendTestEmail'])->name('test-email');
    Route::get('/generate', [StickerController::class, 'create'])->name('create');
    Route::post('/generate', [StickerController::class, 'store'])->name('store');
    Route::get('/lookup', [StickerController::class, 'lookup'])->name('lookup');
    Route::post('/lookup', [StickerController::class, 'search'])->name('search');
    Route::get('/{sticker}', [StickerController::class, 'show'])->name('show');
    Route::get('/{sticker}/download', [StickerController::class, 'downloadPdf'])->name('download');
    Route::get('/{sticker}/print', [StickerController::class, 'printView'])->name('print');
});

Route::post('/api/quotation/from-session', [PublicController::class, 'storeQuotationFromSession'])->middleware('auth')->name('api.quotation.from-session');

Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware(['auth', 'online'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-quote', [QuotationController::class, 'myQuote'])->name('my.quote');
    Route::get('/my-quotations', [QuotationController::class, 'myQuotations'])->name('my.quotations');
    Route::get('/recent-quotations', [QuotationController::class, 'recentQuotations'])->name('recent.quotations');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/verify-email', [ProfileController::class, 'verifyNewEmail'])->name('profile.verify.email');

    Route::get('/quotations/tracker', [QuotationController::class, 'tracker'])->name('quotations.tracker');
    Route::patch('/quotations/{quotation}/update-status', [QuotationController::class, 'updateStatus'])->name('quotations.update-status');
    Route::get('/quotations/preview', [QuotationController::class, 'preview'])->name('quotations.preview');
    Route::resource('quotations', QuotationController::class)->except(['edit', 'update']);
    Route::post('/quotations/calculate', [QuotationController::class, 'calculate'])->name('quotations.calculate');
    Route::get('/quotations/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.download.pdf');
    Route::post('/quotations/{id}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');

    Route::get('/claims/mine', [ClaimsController::class, 'myClaims'])->name('claims.mine');
    Route::get('/claims/lookup-vehicle', [ClaimsController::class, 'lookupVehicle'])->name('claims.lookup-vehicle');
    Route::get('/claims/suggest-vehicles', [ClaimsController::class, 'suggestVehicles'])->name('claims.suggest-vehicles');
    Route::resource('claims', ClaimsController::class);
    Route::match(['post', 'patch'], '/claims/{claim}/update-status', [ClaimsController::class, 'updateStatus'])->name('claims.update-status');
    Route::match(['post', 'patch'], '/claims/{claim}/update-documents-confirmed', [ClaimsController::class, 'updateDocumentsConfirmed'])->name('claims.update-documents-confirmed');
    Route::match(['post', 'patch'], '/claims/{claim}/update-vehicle-assessment', [ClaimsController::class, 'updateVehicleAssessment'])->name('claims.update-vehicle-assessment');

    Route::get('/quotations/regenerate', [QuotationController::class, 'regenerate'])->name('quotations.regenerate.get');

    Route::get('/help', [UserHelpController::class, 'index'])->name('user.help.index');
    Route::post('/help', [UserHelpController::class, 'store'])->name('user.help.store');

    Route::get('/certificates', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');

    Route::get('/claims/tracker', [ClaimsController::class, 'publicTracker'])->name('claims.public-tracker');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('{id}', [NotificationController::class, 'show'])->name('show');
        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('{id}/mark-unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::delete('{id}/trash', [NotificationController::class, 'trash'])->name('trash');
        Route::post('trash-all', [NotificationController::class, 'trashAll'])->name('trash-all');
        Route::post('{id}/restore', [NotificationController::class, 'restore'])->name('restore');
        Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('trash/empty', [NotificationController::class, 'emptyTrash'])->name('empty-trash');
    });

    Route::get('/dashboard/weather', [DashboardController::class, 'weatherJson'])->name('dashboard.weather');

    Route::middleware(['role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import')->middleware('throttle:3,1');
        Route::post('/users/send-credentials', [UserController::class, 'sendCredentials'])->name('users.send-credentials');

        Route::resource('roles', RoleController::class);

        Route::resource('companies', CompanyController::class);
        Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('/companies/{company}/rates', [CompanyController::class, 'rates'])->name('companies.rates');
        Route::post('/companies/{company}/rates', [CompanyController::class, 'addRate'])->name('companies.rates.add');
        Route::patch('/companies/{company}/rates/{rate}', [CompanyController::class, 'updateRate'])->name('companies.rates.update');
        Route::delete('/companies/{company}/rates/{rate}', [CompanyController::class, 'destroyRate'])->name('companies.rates.destroy');
        Route::post('/companies/{company}/rates/import', [CompanyController::class, 'importRates'])->name('companies.rates.import');
        Route::post('/companies/import-rates', [CompanyController::class, 'importBulkCompanies'])->name('companies.import.bulk');

        Route::get('/compare-all', [CompareAllController::class, 'index'])->name('compare-all.index');
        Route::post('/compare-all', [CompareAllController::class, 'update'])->name('compare-all.update');
        Route::delete('/compare-all', [CompareAllController::class, 'destroy'])->name('compare-all.destroy');

        Route::get('/stickers', [StickerAdminController::class, 'index'])->name('stickers.index');
        Route::get('/stickers/tracker', [StickerAdminController::class, 'tracker'])->name('stickers.tracker');
        Route::get('/stickers/update', [StickerAdminController::class, 'updateStickerForm'])->name('stickers.update-form');
        Route::post('/stickers/update', [StickerAdminController::class, 'updateSticker'])->name('stickers.update');
        Route::post('/stickers/search', [StickerAdminController::class, 'searchSticker'])->name('stickers.search');
        Route::get('/stickers/analytics', [App\Http\Controllers\Admin\StickerAnalyticsController::class, 'index'])->name('stickers.analytics');
        Route::get('/stickers/settings', [App\Http\Controllers\Admin\StickerSettingsController::class, 'index'])->name('stickers.settings');
        Route::get('/stickers/email-settings', [App\Http\Controllers\Admin\StickerSettingsController::class, 'emailSettings'])->name('stickers.email-settings');
        Route::post('/stickers/settings/email', [App\Http\Controllers\Admin\StickerSettingsController::class, 'updateEmailConfig'])->name('stickers.settings.email');
        Route::post('/stickers/{sticker}/response', [App\Http\Controllers\Admin\StickerSettingsController::class, 'logResponse'])->name('stickers.response');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate')->middleware('throttle:10,1');
        Route::post('/reports/stickers', [ReportController::class, 'stickerReport'])->name('reports.stickers')->middleware('throttle:10,1');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/stickers/export-csv', [ReportController::class, 'exportStickersCsv'])->name('reports.stickers.export-csv');
        Route::get('/reports/stickers/export-excel', [ReportController::class, 'exportStickersExcel'])->name('reports.stickers.export-excel');
        Route::get('/reports/stickers/export-pdf', [ReportController::class, 'exportStickersPdf'])->name('reports.stickers.export-pdf');

        Route::get('/audit-logs', [AuditLogController::class, 'indexAudit'])->name('audit.index');
        Route::get('/activity-logs', [AuditLogController::class, 'indexActivity'])->name('audit.activity');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('throttle:10,1');
        Route::post('/settings/cache/clear', [SettingController::class, 'clearCache'])->name('settings.cache.clear');
        Route::post('/settings/cache/config', [SettingController::class, 'clearConfigCache'])->name('settings.cache.config');
        Route::post('/settings/cache/view', [SettingController::class, 'clearViewCache'])->name('settings.cache.view');
        Route::post('/settings/cache/route', [SettingController::class, 'clearRouteCache'])->name('settings.cache.route');
        Route::post('/settings/cache/optimize', [SettingController::class, 'optimize'])->name('settings.cache.optimize');
        Route::post('/settings/cache/all', [SettingController::class, 'clearAllCaches'])->name('settings.cache.all');

        Route::get('/email-settings', [EmailSettingController::class, 'index'])->name('email-settings.index');
        Route::post('/email-settings', [EmailSettingController::class, 'store'])->name('email-settings.store');
        Route::patch('/email-settings/{emailSetting}', [EmailSettingController::class, 'update'])->name('email-settings.update');
        Route::delete('/email-settings/{emailSetting}', [EmailSettingController::class, 'destroy'])->name('email-settings.destroy');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [AdminNotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/mark-unread', [AdminNotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
        Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications/clear-all', [AdminNotificationController::class, 'clearAll'])->name('notifications.clear-all');

        Route::get('/help', [HelpController::class, 'index'])->name('help.index');
        Route::get('/help/{enquiry}', [HelpController::class, 'show'])->name('help.show');
        Route::post('/help/{enquiry}/status', [HelpController::class, 'updateStatus'])->name('help.update-status');
        Route::post('/help/{enquiry}/respond', [HelpController::class, 'respond'])->name('help.respond');

        Route::prefix('health')->name('health.')->group(function () {
            Route::get('/', [HealthCheckController::class, 'index'])->name('index');
            Route::post('/run', [HealthCheckController::class, 'run'])->name('run');
        });

        Route::prefix('change-requests')->name('change-requests.')->group(function () {
            Route::get('/', [ChangeRequestController::class, 'index'])->name('index');
            Route::get('/{changeRequest}', [ChangeRequestController::class, 'show'])->name('show');
            Route::patch('/{changeRequest}/approve', [ChangeRequestController::class, 'approve'])->name('approve');
            Route::patch('/{changeRequest}/decline', [ChangeRequestController::class, 'decline'])->name('decline');
        });

        Route::get('/rate-settings', [RateSettingController::class, 'index'])->name('rate-settings.index');

        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
            Route::post('/rename', [MediaController::class, 'rename'])->name('rename');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
            Route::delete('/file/{path}', [MediaController::class, 'destroy'])->name('destroy-file');
            Route::get('/list', [MediaController::class, 'list'])->name('list');
        });

        Route::resource('claims', AdminClaimController::class)->except(['create', 'store']);
        Route::patch('/claims/{claim}/update-status', [AdminClaimController::class, 'updateStatus'])->name('claims.update-status');
    });

});

require __DIR__ . '/auth.php';
