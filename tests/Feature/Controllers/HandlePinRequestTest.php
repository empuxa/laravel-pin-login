<?php

namespace Empuxa\PinLogin\Tests\Feature\Controllers;

use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class HandlePinRequestTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_cannot_login_with_wrong_pin(): void
    {
        $user = $this->createUser();

        $response = $this
            ->withSession([
                config('pin-login.columns.identifier') => $user->{config('pin-login.columns.identifier')},
            ])
            ->post(route('pin-login.pin.handle'), [
                'pin' => [9, 9, 9, 9, 9, 9],
            ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.pin_wrong', [
            'attempts_left' => config('pin-login.pin.max_attempts') - 1,
        ]));

        $this->assertGuest();
    }

    public function test_cannot_login_with_expired_session(): void
    {
        Notification::fake();

        $user = $this->createUser([
            config('pin-login.columns.pin_valid_until') => now()->subSecond(),
        ]);

        $response = $this
            ->withSession([
                config('pin-login.columns.identifier') => $user->{config('pin-login.columns.identifier')},
            ])
            ->post(route('pin-login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.expired'));

        $this->assertGuest();
    }

    public function test_cannot_login_with_rate_limit(): void
    {
        $user = $this->createUser([
            config('pin-login.columns.pin_valid_until') => now()->addMinutes(10),
        ]);

        $session = [
            config('pin-login.columns.identifier') => $user->{config('pin-login.columns.identifier')},
        ];

        for ($i = 0; $i < config('pin-login.pin.max_attempts'); $i++) {
            $this
                ->withSession($session)
                ->post(route('pin-login.pin.handle'), [
                    'pin' => [9, 9, 9, 9, 9, 9],
                ]);
        }

        $response = $this
            ->withSession($session)
            ->post(route('pin-login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors('pin', __('controllers/session.store.error.rate_limit', [
            'seconds' => RateLimiter::availableIn($user->{config('pin-login.columns.identifier')}),
        ]));

        $this->assertGuest();
    }

    public function test_can_login_with_correct_pin(): void
    {
        $user = $this->createUser([
            config('pin-login.columns.pin_valid_until') => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                config('pin-login.columns.identifier') => $user->{config('pin-login.columns.identifier')},
            ])
            ->post(route('pin-login.pin.handle'), [
                'pin' => [1, 2, 3, 4, 5, 6],
            ]);

        $response->assertStatus(302);

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(config('pin-login.redirect'));

        $this->assertAuthenticatedAs($user);
    }
}
