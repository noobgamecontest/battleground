<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function disabled_user_cant_login()
    {
        $user = factory(User::class)->state('disabled')->create();

        $response = $this->get('/login');

        $response->assertViewIs('auth.login');

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function enabled_user_can_login()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
}
