<?php

namespace Empuxa\LoginViaPin\Tests\Feature\Controllers;

use Empuxa\LoginViaPin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowEmailInputTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_can_render_login_screen(): void
    {
        $response = $this->get(route('login.email.show'));

        $response->assertStatus(200);
    }
}
