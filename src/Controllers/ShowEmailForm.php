<?php

namespace Empuxa\LoginViaPin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ShowEmailForm extends Controller
{
    public function __invoke(): View
    {
        return view('login-via-pin::identifier');
    }
}
