<?php

namespace Empuxa\LoginViaPin\Tests\Feature\Controllers;

use Empuxa\LoginViaPin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowIdentifierFormTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_render_login_screen(): void
    {
        $response = $this->get(route('login-via-pin.identifier.show'));

        $response->assertStatus(200);
    }
}
