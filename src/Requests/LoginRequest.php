<?php

namespace Empuxa\LoginViaPin\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_null($this->user);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => config('login-via-pin.email.validation'),
        ];
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $this->checkIfUserExists();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('login-via-pin.email.max_attempts') - 1)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function isUserExistent(): bool
    {
        return config('login-via-pin.model')::query()
            ->where('email', $this->input('email'))
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
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}
