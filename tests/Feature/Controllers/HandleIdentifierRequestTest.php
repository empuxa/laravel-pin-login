<?php

namespace Empuxa\PinLogin\Tests\Feature\Controllers;

use Empuxa\PinLogin\Jobs\CreateAndSendLoginPin;
use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

class HandleIdentifierRequestTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_sends_email_to_user(): void
    {
        Bus::fake();

        $user = $this->createUser();

        $response = $this->post(route('pin-login.identifier.handle'), [
            config('pin-login.columns.identifier') => $user->email,
        ]);

        $response->assertSessionHasNoErrors();

        Bus::assertDispatched(CreateAndSendLoginPin::class);

        $response->assertRedirect(route('pin-login.pin.show'));

        $this->assertGuest();
    }

    public function test_does_not_send_email_to_user_with_wrong_email(): void
    {
        Bus::fake();

        $response = $this->post(route('pin-login.identifier.handle'), [
            config('pin-login.columns.identifier') => 'not_existing@example.com',
        ]);

        $response->assertSessionHasErrors('email', __('auth.failed'));

        Bus::assertNotDispatched(CreateAndSendLoginPin::class);

        $this->assertGuest();
    }

    public function test_does_not_send_email_to_user_with_rate_limit(): void
    {
        Event::fake();

        for ($i = 0; $i < config('pin-login.identifier.max_attempts'); $i++) {
            $this->post(route('pin-login.identifier.handle'), [
                config('pin-login.columns.identifier') => 'non_existing@example.com',
            ]);
        }

        Event::assertDispatched(Lockout::class);

        $this->assertGuest();
    }
}
