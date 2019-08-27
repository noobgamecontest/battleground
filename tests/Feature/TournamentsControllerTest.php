<?php

namespace Tests\Feature;

use App\Services\Message\Message;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_list_tournaments()
    {
        $tournaments = factory(Tournament::class, 2)->create([
            'started_at' => Carbon::tomorrow(),
            'ended_at' => null,
        ]);

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

    /** @test */
    public function admin_can_launch_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create([
            'name' => 'NGC #49',
            'slots' => 16,
            'opponents_by_match' => 4,
            'winners_by_match' => 2,
        ]);
        $tournament->teams()->saveMany(factory(Team::class, 15)->make());

        $response = $this->actingAs($admin)->patch('tournaments/' . $tournament->id . '/launch');

        $response->assertRedirect('tournaments/'. $tournament->id);

        $this->assertDatabaseHas('matches', ['tournament_id' => $tournament->id]);
    }

    /** @test */
    public function user_can_subscribe_to_a_specific_tournament()
    {
        $user = factory(User::class)->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($user)->get('tournaments/' . $tournament->id);

        $response->assertStatus(200);
        $response->assertSee($tournament->name);

        $teamName = md5(time());

        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => $teamName,
        ];

        $response = $this->actingAs($user)->post('tournaments/subscribe', $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('teams', [
            'name' => $teamName,
            'tournament_id' => $tournament->id,
        ]);
    }

    /** @test */
    public function user_cant_subscribe_to_a_specific_tournament_with_existing_team_name()
    {
        $tournament = factory(Tournament::class)->create();
        $user = factory(User::class)->create();

        factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $response = $this->actingAs($user)->post('tournaments/subscribe', $params);
        $response->assertRedirect('tournaments/'. $tournament->id);

        $message = new Message('danger', "Ce nom d'équipe existe déjà");
        $response->assertSessionHas(['message' => $message]);
    }

    /** @test */
    public function user_cant_subscribe_to_a_specific_tournament_with_max_slots()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
        ]);
        $user = factory(User::class)->create();

        factory(Team::class, 4)->create([
            'tournament_id' => $tournament->id,
        ]);

        $params = [
//            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $response = $this->actingAs($user)->post('tournaments/' . $tournament->id . '/subscribe', $params);
        $response->assertRedirect('tournaments/'. $tournament->id);

        $message = new Message('danger', "Toutes les places sont déjà occupées pour ce tournois");
        $response->assertSessionHas(['message' => $message]);
    }

    /** @test */
    public function an_admin_can_delete_team()
    {
        $user = factory(User::class)->state('admin')->create();

        $tournament = factory(Tournament::class)->create();
        $team = factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $response = $this->actingAs($user)->post('tournaments/unsubscribe', ['teamId' => $team->id]);
        $response->assertStatus(302);

        $message = new Message('success', "L'équipe a été désinscrite avec succès");
        $response->assertSessionHas(['message' => $message]);
    }
}
