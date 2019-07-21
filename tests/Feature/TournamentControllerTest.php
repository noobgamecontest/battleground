<?php

namespace Tests\Feature;

use Tests\TestCase;
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
            'teamName' => $teamName,
        ];

        $response = $this->post('tournament/subscribe/' . $tournament->id, $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('teams', [
            'name' => $teamName,
            'tournament_id' => $tournament->id,
        ]);
    }
}
