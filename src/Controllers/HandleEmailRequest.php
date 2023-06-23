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

        $inputOfIdentifier = $request->input(config('login-via-pin.columns.identifier'));

        $user = self::getUser($inputOfIdentifier);

        SendLoginPin::dispatch($user, $request->ip());

        session([
            config('login-via-pin.columns.identifier') => $inputOfIdentifier,
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
