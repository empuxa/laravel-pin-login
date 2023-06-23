<?php

use Empuxa\LoginViaPin\Controllers\HandleIdentifierRequest;
use Empuxa\LoginViaPin\Controllers\HandlePinRequest;
use Empuxa\LoginViaPin\Controllers\ShowIdentifierForm;
use Empuxa\LoginViaPin\Controllers\ShowPinForm;
use Illuminate\Support\Facades\Route;

Route::middleware(config('login-via-pin.route.middleware'))
    ->prefix(config('login-via-pin.route.prefix'))
    ->group(static function (): void {
        Route::get('/', ShowIdentifierForm::class)->name('login-via-pin.identifier.show');
        Route::post('/', HandleIdentifierRequest::class)->name('login-via-pin.identifier.handle');

        Route::get('/pin', ShowPinForm::class)->name('login-via-pin.pin.show');
        Route::post('/pin', HandlePinRequest::class)->name('login-via-pin.pin.handle');
    });
