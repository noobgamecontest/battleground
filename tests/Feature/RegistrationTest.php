<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $userInformations = [
            'email' => 'foo@bar.baz',
            'name' => 'foo bar baz',
            'password' => 'foobarbaz',
            'password_confirmation' => 'foobarbaz'
        ];
        
        $this->post('/register', $userInformations)
            ->assertRedirect('/home');
                
        array_splice($userInformations, 2, 2);
        
        $this->assertDatabaseHas('users', $userInformations);
    }
}
