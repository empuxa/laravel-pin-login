<?php

use Empuxa\PinLogin\Controllers\HandleIdentifierRequest;
use Empuxa\PinLogin\Controllers\HandlePinRequest;
use Empuxa\PinLogin\Controllers\ShowIdentifierForm;
use Empuxa\PinLogin\Controllers\ShowPinForm;
use Illuminate\Support\Facades\Route;

Route::middleware(config('pin-login.route.middleware'))
    ->prefix(config('pin-login.route.prefix'))
    ->group(static function (): void {
        Route::get('/', ShowIdentifierForm::class)->name('pin-login.identifier.form');
        Route::post('/', HandleIdentifierRequest::class)->name('pin-login.identifier.handle');

        Route::get('/pin', ShowPinForm::class)->name('pin-login.pin.form');
        Route::post('/pin', HandlePinRequest::class)->name('pin-login.pin.handle');
    });
