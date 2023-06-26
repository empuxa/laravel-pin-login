<?php

namespace Empuxa\PinLogin\Controllers;

use Empuxa\PinLogin\Events\LoginRequestViaPin;
use Empuxa\PinLogin\Jobs\CreateAndSendLoginPin;
use Empuxa\PinLogin\Requests\IdentifierRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class HandleIdentifierRequest extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(IdentifierRequest $request): RedirectResponse
    {
        $request->authenticate();

        $identifierData = $request->input(config('pin-login.columns.identifier'));

        $user = $request->getUserModel($identifierData);

        CreateAndSendLoginPin::dispatch($user, $request->ip());

        session([
            config('pin-login.columns.identifier') => $identifierData,
        ]);

        event(new LoginRequestViaPin($user, $request->ip()));

        return redirect(route('pin-login.pin.form'));
    }
}
