<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecoverPasswordTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function existing_user_reset_his_password()
    {
        $user = factory(User::class)->create();
                
        $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.sent'));
    }
    
    /** @test */
    public function guest_reset_his_password_with_invalid_email_address()
    {
        $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'foo',
            ])
            ->assertSuccessful()
            ->assertSee(__('validation.email', [
                'attribute' => 'email',
            ]));
    }
    
    /** @test */
    public function guest_reset_his_password_with_valid_email_address()
    {
        $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'foo@bar.baz',
            ])
            ->assertSuccessful()
            ->assertSee(e(__('passwords.user')));
    }
}
