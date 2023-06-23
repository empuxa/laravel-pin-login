<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Exceptions\SessionInformationMissing;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ShowPinForm extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(): View
    {
        throw_unless(session('email'), SessionInformationMissing::class);

        return view('login-via-pin::pin', [
            'email' => session('email'),
        ]);
    }
}
