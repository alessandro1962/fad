<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// OAuth Routes (devono essere nel web.php per avere le sessioni)
Route::get('/auth/google', [App\Http\Controllers\Auth\OAuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [App\Http\Controllers\Auth\OAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

Route::get('/auth/microsoft', [App\Http\Controllers\Auth\OAuthController::class, 'redirectToMicrosoft'])
    ->name('auth.microsoft');

Route::get('/auth/microsoft/callback', [App\Http\Controllers\Auth\OAuthController::class, 'handleMicrosoftCallback'])
    ->name('auth.microsoft.callback');

// Debug endpoint for testing Google OAuth with Postman
Route::get('/debug/google/callback', [App\Http\Controllers\Auth\OAuthController::class, 'debugGoogleCallback'])
    ->name('debug.google.callback');

// Protected routes that require authentication
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    return view('app');
})->name('dashboard');

Route::get('/admin', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    return view('app');
})->name('admin');

// Catch-all route for Vue.js (must be last)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/auth.php';
