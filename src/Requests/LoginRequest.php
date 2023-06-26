<?php

namespace Empuxa\PinLogin\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guest();
    }

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
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('pin-login.identifier.max_attempts') - 1)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            config('pin-login.columns.identifier') => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function isUserExistent(): bool
    {
        return config('pin-login.model')::query()
            ->where(
                config('pin-login.columns.identifier'),
                $this->input(config('pin-login.columns.identifier')),
            )
            ->exists();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkIfUserExists(): void
    {
        if (! $this->isUserExistent()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                config('pin-login.columns.identifier') => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input(config('pin-login.columns.identifier'))) . '|' . $this->ip();
    }
}
