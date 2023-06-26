<?php

namespace Empuxa\PinLogin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ShowIdentifierForm extends Controller
{
    public function __invoke(): View
    {
        return view('pin-login::identifier');
    }
}
