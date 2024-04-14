<?php

use Illuminate\Support\Facades\Route;
use LaravelOIDCAuth\Http\Controllers\CallbackController;
use LaravelOIDCAuth\Http\Controllers\OIDCAuthController;

Route::middleware('web')->as('oidc.')->group(function () {
    Route::get(config('oidc-auth.callback_route'), [CallbackController::class, 'callback'])->name('callback');
    Route::get('login', [OIDCAuthController::class, 'login'])->name('login');
    Route::post('logout', [OIDCAuthController::class, 'logout'])->name('logout');
});
