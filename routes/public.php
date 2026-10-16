<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\VehicleDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/compare-all', fn () => redirect()->route('generate.quote', 'compare-all'))->name('compare-all');

Route::view('/terms-of-use', 'legal.terms')->name('legal.terms');
Route::view('/privacy-policy', 'legal.privacy')->name('legal.privacy');
// Old paths, kept as redirects in case anything already linked to them.
Route::redirect('/terms', '/terms-of-use', 301);
Route::redirect('/privacy', '/privacy-policy', 301);

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

Route::prefix('motor-quote')->name('motor.')->middleware('throttle:30,1')->group(function () {
    Route::post('/lookup-quotation', [PublicController::class, 'lookupQuotation'])->name('lookup.quotation');
    Route::post('/lookup-sticker', [PublicController::class, 'lookupSticker'])->name('lookup.sticker');
    Route::get('/search', [PublicController::class, 'globalSearch'])->name('search')->middleware('auth');
});

Route::prefix('lookup')->name('public.')->middleware('throttle:30,1')->group(function () {
    Route::get('/quotation/{number}', [PublicController::class, 'showQuotation'])->name('quotation.show');
    Route::get('/sticker/{number}', [PublicController::class, 'showSticker'])->name('sticker.show');
    Route::get('/quotation/{number}/download', [PublicController::class, 'downloadQuotationPdf'])->name('quotation.download');
    Route::post('/quotation/email', [PublicController::class, 'sendQuotationEmail'])->name('quotation.email')->middleware('throttle:5,1');
});

Route::post('/quotations/regenerate', [QuotationController::class, 'regenerate'])->name('quotations.regenerate');

// Real PDF generation for the public/pre-save quote page (resources/views/quotation.blade.php).
// Builds an unsaved Quotation from posted vehicle/customer fields — no auth required, since
// that page itself is guest-accessible — and renders it through the same templates a saved
// quotation uses, so "Preview" shows the actual document rather than a JS-built approximation.
Route::middleware(['throttle:20,1', 'recaptcha'])->group(function () {
    Route::post('/quotation/pdf/preview', [QuotationController::class, 'quotationPdfPreview'])->name('quotation.pdf.preview');
    Route::post('/quotation/pdf/download', [QuotationController::class, 'quotationPdfDownload'])->name('quotation.pdf.download');
    Route::post('/quotation/compare-all/pdf/preview', [QuotationController::class, 'compareAllPdfPreview'])->name('quotation.compare-all.pdf.preview');
    Route::post('/quotation/compare-all/pdf/download', [QuotationController::class, 'compareAllPdfDownload'])->name('quotation.compare-all.pdf.download');
});
Route::post('/quotation/compare-all/pdf/email', [QuotationController::class, 'compareAllPdfEmail'])->name('quotation.compare-all.pdf.email')->middleware('throttle:5,1');

// Public sticker verification only — anyone with a sticker number/id can look it up,
// mirroring how a physical insurance sticker is meant to be independently verifiable.
Route::prefix('stickers')->name('stickers.')->group(function () {
    Route::get('/user-lookup', [StickerController::class, 'userLookup'])->name('user-lookup');
    Route::get('/lookup', [StickerController::class, 'lookup'])->name('lookup');
    Route::post('/lookup', [StickerController::class, 'search'])->name('search')->middleware('throttle:30,1');
    Route::post('/lookup/search-quotation', [StickerController::class, 'searchQuotationPublic'])->name('lookup.search-quotation')->middleware('throttle:30,1');
    Route::get('/{sticker}', [StickerController::class, 'show'])->name('show');
    Route::get('/{sticker}/print', [StickerController::class, 'printView'])->name('print');
});
