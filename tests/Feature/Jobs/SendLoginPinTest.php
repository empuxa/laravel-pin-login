<?php

namespace Empuxa\LoginViaPin\Tests\Feature\Jobs;

use Empuxa\LoginViaPin\Jobs\SendLoginPin;
use Empuxa\LoginViaPin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class SendLoginPinTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_send_notification(): void
    {
        Notification::fake();

        $user = $this->createUser([
            config('login-via-pin.columns.pin_valid_until') => now(),
        ]);

        $this->assertFalse($user->{config('login-via-pin.columns.pin_valid_until')}->isFuture());

        $userLoginPin = $user->{config('login-via-pin.columns.pin')};
        $userUpdatedAt = $user->updated_at;

        SendLoginPin::dispatchSync($user);

        $user->fresh();

        $this->assertTrue($user->{config('login-via-pin.columns.pin_valid_until')}->isFuture());

        // @todo fix this assignment
        $this->assertEquals($userUpdatedAt, $user->updated_at);

        $this->assertNotEquals($userLoginPin, $user->{config('login-via-pin.columns.pin')});
    }
}
