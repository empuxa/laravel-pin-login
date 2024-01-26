<?php

namespace Empuxa\PinLogin\Controllers;

use Empuxa\PinLogin\Jobs\ResetLoginPin;
use Empuxa\PinLogin\Requests\PinRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HandlePinRequest extends Controller
{
    protected ?string $pin = null;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    /**
     * @throws \Throwable
     */
    public function __invoke(PinRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->user = $request->getUserModel(
            session(config('pin-login.columns.identifier')),
        );

        Auth::login($this->user, $request->input('remember') === 'true' ?? false);

        ResetLoginPin::dispatch($this->user);

        $event = config('pin-login.events.logged_in_via_pin');
        event(new $event($this->user, $request));

        return redirect()
            ->intended(config('pin-login.redirect'))
            ->with([
                'message' => __('pin-login::controller.handle_pin_request.success'),
            ]);
    }
}
