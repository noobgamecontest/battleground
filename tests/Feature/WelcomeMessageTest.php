<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_have_a_welcome_message()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertSessionHas('message');
        $response->assertSeeText('Bienvenue !');
    }
}
