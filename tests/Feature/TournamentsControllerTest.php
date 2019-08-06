<?php

namespace Tests\Feature;

use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_list_tournaments()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournaments = factory(Tournament::class, 2)->create();

        $response = $this->actingAs($admin)->get('tournaments');

        $response->assertSeeText($tournaments->first()->name);
        $response->assertSeeText($tournaments->last()->name);
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_show_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->get('tournaments/' . $tournament->id);

        $response->assertSeeText($tournament->name);
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_one()
    {
        $admin = factory(User::class)->state('admin')->create();

        $response = $this->actingAs($admin)->get('tournaments/create');

        $response->assertSeeText("Création d'un tournoi");
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->post('tournaments', [
            'name' => 'Easy Contest',
        ]);

        $response->assertRedirect('tournaments/1');
    }
}
