<?php

namespace Empuxa\PinLogin\Controllers;

use Empuxa\PinLogin\Events\LoginRequestViaPin;
use Empuxa\PinLogin\Jobs\CreateAndSendLoginPin;
use Empuxa\PinLogin\Requests\LoginRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class HandleIdentifierRequest extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $inputOfIdentifier = $request->input(config('pin-login.columns.identifier'));

        $user = self::getUser($inputOfIdentifier);

        CreateAndSendLoginPin::dispatch($user, $request->ip());

        session([
            config('pin-login.columns.identifier') => $inputOfIdentifier,
        ]);

        event(new LoginRequestViaPin($user, $request->ip()));

        return redirect(route('pin-login.pin.show'));
    }

    public static function getUser(string $identifier): Model
    {
        return config('pin-login.model')::query()
            ->where(config('pin-login.columns.identifier'), $identifier)
            ->first();
    }
}
