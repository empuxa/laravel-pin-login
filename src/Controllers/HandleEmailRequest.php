<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Events\LoginRequestViaPin;
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

        $user = self::getUser($request->input(config('login-via-pin.columns.identifier')));

        SendLoginPin::dispatch($user, $request->ip());

        session([
            config('login-via-pin.columns.identifier') => $request->input(config('login-via-pin.columns.identifier')),
        ]);

        event(new LoginRequestViaPin($user, $request->ip()));

        return redirect(route('login.pin.show'));
    }

    public static function getUser(string $identifier): Model
    {
        return config('login-via-pin.model')::query()
            ->where(config('login-via-pin.columns.identifier'), $identifier)
            ->first();
    }
}
