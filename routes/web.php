<?php

use Empuxa\LoginViaPin\Controllers\HandleEmailRequest;
use Empuxa\LoginViaPin\Controllers\HandlePinRequest;
use Empuxa\LoginViaPin\Controllers\ShowEmailForm;
use Empuxa\LoginViaPin\Controllers\ShowPinForm;
use Illuminate\Support\Facades\Route;

Route::middleware(config('login-via-pin.route.middleware'))
    ->prefix(config('login-via-pin.route.prefix'))
    ->group(static function (): void {
        Route::get('/', ShowEmailForm::class)->name('login.email.show');
        Route::post('/', HandleEmailRequest::class)->name('login.email.handle');

        Route::get('/pin', ShowPinForm::class)->name('login.pin.show');
        Route::post('/pin', HandlePinRequest::class)->name('login.pin.handle');
    });
