<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Exceptions\MissingSessionInformation;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ShowPinForm extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(): View
    {
        throw_unless(session(config('login-via-pin.columns.identifier')), MissingSessionInformation::class);

        return view('login-via-pin::pin', [
            config('login-via-pin.columns.identifier') => session(config('login-via-pin.columns.identifier')),
        ]);
    }
}
