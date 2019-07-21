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

        $response = $this->get('tournament/index');

        $response->assertStatus(200);
        $response->assertSee($tournament->name);
    }

    /** @test */
    public function userCanGoToASpecificTournament()
    {
        $tournament = factory(Tournament::class)->create();

        $response = $this->get('tournament/show/' . $tournament->id);

        $response->assertStatus(200);
        $response->assertSee($tournament->name);

        $params = [
            'tournamentId' => $tournament->id,
            'name' => 'Marky',
        ];

        $response = $this->post('tournament/register', $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('teams', [
            'name' => 'Marky',
            'tournament_id' => $tournament->id,
        ]);
    }
}
