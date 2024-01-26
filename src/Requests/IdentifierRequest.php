<?php

namespace Empuxa\PinLogin\Requests;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class IdentifierRequest extends BaseRequest
{
    /**
     * @return array<int|string, mixed>
     */
    public function rules(): array
    {
        return [
            config('pin-login.columns.identifier') => config('pin-login.identifier.validation'),
        ];
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // This check might not be required depending on your validation rules
        $this->checkIfUserExists();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (config('pin-login.identifier.enable_throttling', true) === false) {
            return;
        }

        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('pin-login.identifier.max_attempts') - 1)) {
            return;
        }

        $event = config('pin-login.events.lockout');
        event(new $event($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            config('pin-login.columns.identifier') => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkIfUserExists(): void
    {
        if (! is_null($this->getUserModel())) {
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            config('pin-login.columns.identifier') => __('auth.failed'),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input(config('pin-login.columns.identifier'))) . '|' . $this->ip();
    }
}
