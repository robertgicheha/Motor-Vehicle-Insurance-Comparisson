<?php

use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
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
use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'online', 'role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Registered before the users resource so the static /users/trash path
    // isn't swallowed by the resource's /users/{user} wildcard route.
    Route::get('/users/trash', [UserController::class, 'trash'])->name('users.trash')->middleware('role:super-admin');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore')->middleware('role:super-admin');

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
    Route::get('/audit-logs/story', [AuditLogController::class, 'userStory'])->name('audit.story');

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
    Route::get('/claims-email-logs', [AdminClaimController::class, 'emailLogs'])->name('claims.email-logs');
});
