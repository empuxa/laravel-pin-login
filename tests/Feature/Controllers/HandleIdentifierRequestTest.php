<?php

namespace Empuxa\PinLogin\Tests\Feature\Controllers;

use Empuxa\PinLogin\Notifications\LoginPin;
use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class HandleIdentifierRequestTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_sends_email_to_user(): void
    {
        Notification::fake();

        $user = $this->createUser();

        $response = $this->post(route('pin-login.identifier.handle'), [
            config('pin-login.columns.identifier') => $user->email,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(route('pin-login.pin.form'));

        $this->assertGuest();

        Notification::assertSentTo($user, LoginPin::class);
    }

    public function test_does_not_send_email_to_user_with_wrong_email(): void
    {
        Notification::fake();

        $response = $this->post(route('pin-login.identifier.handle'), [
            config('pin-login.columns.identifier') => 'not_existing@example.com',
        ]);

        $response->assertSessionHasErrors('email', __('auth.failed'));

        $this->assertGuest();

        Notification::assertNothingSent();
    }

    public function test_does_not_send_email_to_user_with_rate_limit(): void
    {
        Config::set('pin-login.identifier.enable_throttling', true);

        Event::fake();
        Notification::fake();

        for ($i = 0; $i < config('pin-login.identifier.max_attempts'); $i++) {
            $this->post(route('pin-login.identifier.handle'), [
                config('pin-login.columns.identifier') => 'non_existing@example.com',
            ]);
        }

        $event = config('pin-login.events.lockout');
        Event::assertDispatched($event);

        $this->assertGuest();

        Notification::assertNothingSent();
    }

    public function test_sends_email_to_user_through_disabled_rate_limit(): void
    {
        Config::set('pin-login.identifier.enable_throttling', false);

        Notification::fake();

        $user = $this->createUser();

        for ($i = 0; $i < config('pin-login.identifier.max_attempts'); $i++) {
            $this->post(route('pin-login.identifier.handle'), [
                config('pin-login.columns.identifier') => 'non_existing@example.com',
            ]);
        }

        $response = $this->post(route('pin-login.identifier.handle'), [
            config('pin-login.columns.identifier') => $user->email,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(route('pin-login.pin.form'));

        $this->assertGuest();

        Notification::assertSentTo($user, LoginPin::class);
    }
}
