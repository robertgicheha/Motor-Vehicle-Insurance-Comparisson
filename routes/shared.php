<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\User\HelpController as UserHelpController;
use App\Http\Controllers\ClaimsController;
use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/quotation/{quotation}/download-pdf', [QuotationController::class, 'publicDownloadPdf'])->name('quotation.download.pdf')->middleware(['auth', 'online']);

// Sticker issuance workflow — requires login. Registered before the public wildcard
// group in routes/public.php, since a static path like /stickers/issue would otherwise
// be swallowed by the /stickers/{sticker} route (Laravel matches routes in registration order).
Route::middleware(['auth', 'online'])->prefix('stickers')->name('stickers.')->group(function () {
    Route::post('/lookup-by-quotation', [StickerController::class, 'lookupByQuotation'])->name('lookup-by-quotation');
    Route::get('/issue', [StickerController::class, 'issueForm'])->name('issue');
    Route::post('/search-quotation', [StickerController::class, 'searchQuotation'])->name('search-quotation');
    Route::post('/cover-details', [StickerController::class, 'saveCoverDetails'])->name('cover-details');
    Route::post('/calculate-cover-end-date', [StickerController::class, 'calculateCoverEndDate'])->name('calculate-cover-end-date');
    Route::post('/toggle-logbook-na', [StickerController::class, 'toggleLogbookNA'])->name('toggle-logbook-na');
    Route::post('/upload-document', [StickerController::class, 'uploadDocument'])->name('upload-document');
    Route::post('/remove-document', [StickerController::class, 'removeDocument'])->name('remove-document');
    Route::post('/scan-status', [StickerController::class, 'scanStatus'])->name('scan-status');
    Route::post('/issue', [StickerController::class, 'issue'])->name('issue.store');
    Route::get('/generate', [StickerController::class, 'create'])->name('create');
    Route::post('/generate', [StickerController::class, 'store'])->name('store');
    Route::get('/my-stickers', [StickerController::class, 'myStickers'])->name('my-stickers');
    Route::get('/my-updates', [StickerController::class, 'myUpdateForm'])->name('my-updates');
    Route::post('/my-updates/search', [StickerController::class, 'mySearchSticker'])->name('my-updates.search');
    Route::post('/my-updates/complete', [StickerController::class, 'myUpdateSticker'])->name('my-updates.complete');
});

Route::post('/api/quotation/from-session', [PublicController::class, 'storeQuotationFromSession'])->middleware(['auth', 'throttle:15,1', 'recaptcha'])->name('api.quotation.from-session');

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
    Route::get('/quotations/compare-all', [QuotationController::class, 'compareAll'])->name('quotations.compare-all');
    Route::post('/quotations/compare-all/calculate', [QuotationController::class, 'compareAllCalculate'])->name('quotations.compare-all.calculate');
    Route::post('/quotations/compare-all/pdf/preview', [QuotationController::class, 'compareAllPdfPreview'])->name('quotations.compare-all.pdf.preview')->middleware(['throttle:20,1', 'recaptcha']);
    Route::post('/quotations/compare-all/pdf/download', [QuotationController::class, 'compareAllPdfDownload'])->name('quotations.compare-all.pdf.download')->middleware(['throttle:20,1', 'recaptcha']);
    Route::resource('quotations', QuotationController::class)->except(['edit', 'update'])->middlewareFor('store', ['throttle:10,1', 'recaptcha']);
    Route::post('/quotations/calculate', [QuotationController::class, 'calculate'])->name('quotations.calculate');
    Route::get('/quotations/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.download.pdf');
    Route::get('/quotations/{quotation}/preview-pdf', [QuotationController::class, 'previewPdf'])->name('quotations.preview.pdf');
    Route::post('/quotations/{id}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');

    Route::get('/claims/mine', [ClaimsController::class, 'myClaims'])->name('claims.mine');
    Route::get('/claims/lookup-vehicle', [ClaimsController::class, 'lookupVehicle'])->name('claims.lookup-vehicle');
    Route::get('/claims/suggest-vehicles', [ClaimsController::class, 'suggestVehicles'])->name('claims.suggest-vehicles');
    Route::post('/claims/upload-document', [ClaimsController::class, 'uploadDocument'])->name('claims.upload-document');
    Route::post('/claims/remove-document', [ClaimsController::class, 'removeDocument'])->name('claims.remove-document');
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
    Route::get('/dashboard/activity-feed', [DashboardController::class, 'activityFeed'])->name('dashboard.activity-feed');
});
