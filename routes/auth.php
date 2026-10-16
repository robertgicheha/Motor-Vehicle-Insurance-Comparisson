<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Accounts are username + password only. Email-based features (registration,
| email verification) and social sign-in have been removed to avoid storing
| personal email addresses. Accounts are provisioned by an administrator;
| passwords are issued and must be changed within 30 days.
|
| Password reset is self-service from the login page: a user confirms identity
| with their username and the password they currently hold, then sets a new
| strong password (which restarts the 30-day expiry). A completely forgotten
| password is reset by an administrator from User Management.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(['throttle:5,1', 'recaptcha']);

    Route::get('password/reset', [PasswordResetController::class, 'show'])
        ->name('password.reset');

    Route::post('password/reset', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.reset.submit');

    Route::get('password/reset/success', [PasswordResetController::class, 'success'])
        ->name('password.reset.success');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
