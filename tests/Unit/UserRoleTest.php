<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanBeAnAdmin()
    {
        $user = factory(User::class)->state('admin')->create();

        $this->assertEquals('admin', $user->role);

        $this->assertTrue($user->isAdmin());
    }

    /** @test */
    public function userCanBeASimpleUser()
    {
        $user = factory(User::class)->create();

        $this->assertEquals('user', $user->role);

        $this->assertFalse($user->isAdmin());
    }
}
