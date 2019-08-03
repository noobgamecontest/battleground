<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guestCanSeeTheTournamentList()
    {
        $tournament = factory(Tournament::class)->create();

        $response = $this->get('tournament');

        $response->assertStatus(200);
        $response->assertSee($tournament->name);
    }

    /** @test */
    public function guestCanSubscribeToASpecificTournament()
    {
        $tournament = factory(Tournament::class)->create();

        $response = $this->get('tournament/show/' . $tournament->id);

        $response->assertStatus(200);
        $response->assertSee($tournament->name);

        $teamName = md5(time());

        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => $teamName,
        ];

        $response = $this->post('tournament/subscribe', $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('teams', [
            'name' => $teamName,
            'tournament_id' => $tournament->id,
        ]);
    }

    /** @test */
    public function guestCantSubscribeToASpecificTournamentWithExistingTeamName()
    {
        $tournament = factory(Tournament::class)->create();
        factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $response = $this->post('tournament/subscribe', $params);
        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'teamName' => 'This team name already exists',
        ]);
    }

    /** @test */
    public function guestCantDeleteTeam()
    {
        $tournament = factory(Tournament::class)->create();
        $team = factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $response = $this->post('tournament/deleteTeam', ['teamId' => $team->id]);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function anUnauthorizedUserCantDeleteTeam()
    {
        $user = factory(User::class)->create();

        $tournament = factory(Tournament::class)->create();
        $team = factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $response = $this->actingAs($user)->post('tournament/deleteTeam', ['teamId' => $team->id]);
        $response->assertStatus(403);
    }

    /** @test */
    public function anAdminCanDeleteTeam()
    {
        $user = factory(User::class)->state('admin')->create();

        $tournament = factory(Tournament::class)->create();
        $team = factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $response = $this->actingAs($user)->post('tournament/deleteTeam', ['teamId' => $team->id]);

        $response->assertStatus(302);
        $response->assertSessionHas(['alert-success' => 'Team was successful deleted']);
    }
}
