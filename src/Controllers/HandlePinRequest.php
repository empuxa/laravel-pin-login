<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Events\LoggedInViaPin;
use Empuxa\LoginViaPin\Exceptions\MissingSessionInformation;
use Empuxa\LoginViaPin\Jobs\ResetLoginPin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HandlePinRequest extends Controller
{
    protected ?string $pin = null;

    protected $user;

    public static function getUserByIdentifier(): Model
    {
        return config('login-via-pin.model')::query()
            ->where(config('login-via-pin.columns.identifier'), session(config('login-via-pin.columns.identifier')))
            ->firstOrFail();
    }

    /**
     * @todo use PIN request class
     *
     * @throws \Throwable
     */
    public function __invoke(Request $request): RedirectResponse
    {
        throw_unless(session(config('login-via-pin.columns.identifier')), MissingSessionInformation::class);

        $this->user = self::getUserByIdentifier();

        $this->ensureRequestIsNotRateLimited();
        $this->ensurePinIsNotExpired();
        $this->validatePin($request->input('pin'));

        $request->session()->regenerate();

        Auth::login($this->user, $request->input('remember') ?? false);

        RateLimiter::clear($this->throttleKey());
        ResetLoginPin::dispatch($this->user);

        event(new LoggedInViaPin($this->user, $request->ip()));

        return redirect(config('login-via-pin.redirect'))->with([
            // @todo i18n
            'message' => __('controllers/session.store.success'),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureRequestIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('login-via-pin.pin.max_attempts') - 1)) {
            return;
        }

        throw ValidationException::withMessages([
            // @todo i18n
            'pin' => __('controllers/session.store.error.rate_limit', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
            ]),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensurePinIsNotExpired(): void
    {
        if (now() < $this->user->{config('login-via-pin.columns.pin_valid_until')}) {
            return;
        }

        throw ValidationException::withMessages([
            // @todo i18n
            'pin' => __('controllers/session.store.error.expired'),
        ]);
    }

    /**
     * @param array<int, int|string> $pin
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatePin(array $pin): void
    {
        collect($pin)->each(function ($digit): void {
            $this->pin .= (int) $digit;
        });

        if (Hash::check($this->pin, $this->user->{config('login-via-pin.columns.pin')})) {
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            // @todo i18n
            'pin' => __('controllers/session.store.error.pin_wrong', [
                'attempts_left' => config('login-via-pin.pin.max_attempts') - RateLimiter::attempts(
                    $this->throttleKey()
                ),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->user->{config('login-via-pin.columns.identifier')});
    }
}
