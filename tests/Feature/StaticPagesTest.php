<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_pages_are_publicly_available(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('About Anim24');

        $this->get(route('privacy-policy'))
            ->assertOk()
            ->assertSee('Privacy Policy');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('contact@anim24.com');
    }
}
