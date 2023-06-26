<?php

namespace Empuxa\PinLogin\Controllers;

use Empuxa\PinLogin\Exceptions\MissingSessionInformation;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ShowPinForm extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(): View
    {
        throw_unless(session(config('pin-login.columns.identifier')), MissingSessionInformation::class);

        return view('pin-login::pin', [
            config('pin-login.columns.identifier') => session(config('pin-login.columns.identifier')),
        ]);
    }
}
