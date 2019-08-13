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
    public function guest_can_list_tournaments()
    {
        $tournaments = factory(Tournament::class, 2)->create();

        $response = $this->get('/');

        $response->assertSeeText($tournaments->first()->name);
        $response->assertSeeText($tournaments->last()->name);
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_show_one()
    {
        $admin = factory(User::class)->create();
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
        
        $properties = [
            'name' => 'Easy Contest',
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,            
        ];

        $response = $this->actingAs($admin)->post('tournaments', $properties);
        
        $response->assertRedirect('tournaments/1');
        
        $this->assertDatabaseHas('tournaments', $properties);
    }

    /** @test */
    public function admin_can_update_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->get('tournaments/' . $tournament->id . '/edit');

        $response->assertSeeText("Edition d'un tournoi");
        $response->assertStatus(200);

        $properties = [
            'name' => 'Easy Contest',
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => null,
        ];

        $response = $this->actingAs($admin)->put('tournaments/' . $tournament->id, $properties);

        $response->assertRedirect('tournaments/' . $tournament->id);

        $this->assertDatabaseHas('tournaments', ['id' => $tournament->id] + $properties);
    }

    /** @test */
    public function admin_can_delete_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->delete('tournaments/' . $tournament->id);

        $response->assertRedirect('/');

        $this->assertDatabaseMissing('tournaments', ['id' => $tournament->id]);
    }
}
