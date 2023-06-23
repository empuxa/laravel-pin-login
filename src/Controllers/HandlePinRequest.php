<?php

namespace Empuxa\LoginViaPin\Controllers;

use Empuxa\LoginViaPin\Exceptions\SessionInformationMissing;
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

    public static function getUserByEmail(): Model
    {
        return config('login-via-pin.model')::query()
            ->where('email', session('email'))
            ->firstOrFail();
    }

    /**
     * @todo use PIN request
     *
     * @throws \Throwable
     */
    public function __invoke(Request $request): RedirectResponse
    {
        throw_unless(session('email'), SessionInformationMissing::class);

        $this->user = self::getUserByEmail();

        $this->ensureIsNotRateLimited();
        $this->ensurePinIsNotExpired($request->ip());
        $this->validatePin($request->input('code'));

        $request->session()->regenerate();

        Auth::login($this->user, $request->input('remember') ?? false);

        RateLimiter::clear($this->throttleKey());

        ResetLoginPin::dispatch($this->user);

        return redirect(config('login-via-pin.redirect'))->with([
            // @todo i18n
            'message' => __('controllers/session.store.success'),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('login-via-pin.pin.max_attempts') - 1)) {
            return;
        }

        throw ValidationException::withMessages([
            // @todo i18n & test
            'pin' => __('controllers/session.store.error.rate_limit', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
            ]),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensurePinIsNotExpired(string $ip): void
    {
        if (now() < $this->user->{config('login-via-pin.columns.pin_valid_until')}) {
            return;
        }

        throw ValidationException::withMessages([
            // @todo i18n & test
            'pin' => __('controllers/session.store.error.expired'),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatePin(array $code): void
    {
        collect($code)->each(function ($digit): void {
            $this->pin .= $digit;
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
        return Str::lower($this->user->email);
    }
}
