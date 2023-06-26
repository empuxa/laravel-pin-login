<?php

namespace Empuxa\PinLogin\Tests\Feature\Jobs;

use Empuxa\PinLogin\Jobs\ResetLoginPin;
use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetLoginPinTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_reset_the_pin(): void
    {
        $user = $this->createUser([
            config('pin-login.columns.pin_valid_until') => now()->addMinutes(10),
        ]);

        $userUpdatedAt = $user->updated_at;

        $this->assertTrue($user->login_pin_valid_until->isFuture());

        ResetLoginPin::dispatchSync($user);

        $user->fresh();

        $this->assertFalse($user->{config('pin-login.columns.pin_valid_until')}->isFuture());

        // Timestamps have not been updated
        $this->assertEquals($userUpdatedAt, $user->updated_at);
    }
}
