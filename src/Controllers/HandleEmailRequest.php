<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Jobs\SendLoginPin;
use Empuxa\LoginViaPin\Requests\LoginRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class HandleEmailRequest extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        SendLoginPin::dispatch(self::getUser($request->input('email')), $request->ip());

        session(['email' => $request->input('email')]);

        return redirect(route('login.pin.show'));
    }

    public static function getUser(string $email): Model
    {
        return config('login-via-pin.model')::where('email', $email)->first();
    }
}
