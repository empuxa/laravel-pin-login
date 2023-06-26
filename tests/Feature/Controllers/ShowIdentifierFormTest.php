<?php

namespace Empuxa\PinLogin\Tests\Feature\Controllers;

use Empuxa\PinLogin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowIdentifierFormTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_render_login_screen(): void
    {
        $response = $this->get(route('pin-login.identifier.show'));

        $response->assertStatus(200);
    }
}
