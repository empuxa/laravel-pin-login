<?php

namespace Empuxa\LoginViaPin\Tests\Feature\Controllers;

use Empuxa\LoginViaPin\Tests\TestbenchTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowPinInputTest extends TestbenchTestCase
{
    use RefreshDatabase;

    public function test_cannot_render_pin_screen_because_of_missing_session(): void
    {
        $response = $this->get(route('login.pin.show'));

        $response->assertStatus(500);
    }

    public function test_can_render_pin_screen(): void
    {
        $response = $this
            ->withSession(['email' => 'admin@example.com'])
            ->get(route('login.pin.show'));

        $response->assertStatus(200);
    }
}
