<?php

namespace Empuxa\LoginViaPin\Tests\Feature\Controllers;

use Empuxa\LoginViaPin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

class HandlePinRequestTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_cannot_login_with_wrong_pin(): void
    {
        $user = $this->createUser();

        $response = $this
            ->withSession([
                config('login-via-pin.columns.identifier') => $user->{config('login-via-pin.columns.identifier')},
            ])
            ->post(route('login.pin.handle'), [
                'pin' => [9, 9, 9, 9, 9, 9],
            ]);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.pin_wrong', [
            'attempts_left' => config('login-via-pin.pin.max_attempts') - 1,
        ]));
    }

    public function test_cannot_login_with_expired_session(): void
    {
        $user = $this->createUser();

        $response = $this
            ->withSession([
                config('login-via-pin.columns.identifier') => $user->{config('login-via-pin.columns.identifier')},
            ])
            ->post(route('login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.expired'));
    }

    public function test_cannot_login_with_rate_limit(): void
    {
        $user = $this->createUser([
            config('login-via-pin.columns.pin_valid_until') => now()->addMinutes(10),
        ]);

        $session = [
            config('login-via-pin.columns.identifier') => $user->{config('login-via-pin.columns.identifier')},
        ];

        for ($i = 0; $i < config('login-via-pin.pin.max_attempts'); $i++) {
            $this
                ->withSession($session)
                ->post(route('login.pin.handle'), [
                    'pin' => [9, 9, 9, 9, 9, 9],
                ]);
        }

        $response = $this
            ->withSession($session)
            ->post(route('login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.rate_limit', [
            'seconds' => RateLimiter::availableIn($user->{config('login-via-pin.columns.identifier')}),
        ]));
    }

    public function test_can_login_with_correct_pin(): void
    {
        $user = $this->createUser([
            config('login-via-pin.columns.pin_valid_until') => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                config('login-via-pin.columns.identifier') => $user->{config('login-via-pin.columns.identifier')},
            ])
            ->post(route('login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(config('login-via-pin.redirect'));
    }
}
