<?php

namespace Empuxa\PinLogin\Tests\Feature\Jobs;

use Empuxa\PinLogin\Jobs\CreateAndSendLoginPin;
use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class SendLoginPinTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_send_notification(): void
    {
        Notification::fake();

        $user = $this->createUser([
            config('pin-login.columns.pin_valid_until') => now(),
        ]);

        $this->assertFalse($user->{config('pin-login.columns.pin_valid_until')}->isFuture());

        $userLoginPin = $user->{config('pin-login.columns.pin')};
        $userUpdatedAt = $user->updated_at;

        CreateAndSendLoginPin::dispatchSync($user);

        $user->fresh();

        $this->assertTrue($user->{config('pin-login.columns.pin_valid_until')}->isFuture());

        // @todo fix this assignment
        // $this->assertEquals($userUpdatedAt, $user->updated_at);

        $this->assertNotEquals($userLoginPin, $user->{config('pin-login.columns.pin')});
    }
}
