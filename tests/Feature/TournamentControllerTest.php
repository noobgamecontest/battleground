<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanSeeTheTournamentList()
    {
        $tournament = factory(Tournament::class)->create();

        $response = $this->get('tournament');

        $response->assertStatus(200);
        $response->assertSee($tournament->name);
    }

    /** @test */
    public function userCanSubscribeToASpecificTournament()
    {
        $tournament = factory(Tournament::class)->create();

        $response = $this->get('tournament/show/' . $tournament->id);

        $response->assertStatus(200);
        $response->assertSee($tournament->name);

        $teamName = md5(time());

        $params = [
            'id' => $tournament->id,
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
    public function userCantSubscribeToASpecificTournamentWithExistingTeamName()
    {
        $tournament = factory(Tournament::class)->create();
        factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $params = [
            'id' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $response = $this->post('tournament/subscribe', $params);
        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'teamName' => 'This team name already exists',
        ]);
    }

    /** @test */
//    public function adminCanDeleteATeamFromTournament()
//    {
//
//    }
}
