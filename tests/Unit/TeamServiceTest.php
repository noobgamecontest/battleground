<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Services\TeamService $service
     */
    protected $service;

    /**
     * Setting up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new TeamService();
    }

    /** @test */
    public function canCreateTeam()
    {
        $tournament = factory(Tournament::class)->create();

        $this->service->create($tournament, 'nameForTest');

        $this->assertDatabaseHas('teams', [
            'name' => 'nameForTest',
            'tournament_id' => $tournament->id,
        ]);
    }

    /** @test */
    public function canDeleteTeam()
    {
        $tournament = factory(Tournament::class)->create();
        $team = factory(Team::class)->create([
            'name' => 'nameForTest',
            'tournament_id' => $tournament->id,
        ]);

        $this->service->delete($team->id);

        $this->assertDatabaseMissing('teams', [
            'name' => 'nameForTest',
            'tournament_id' => $tournament->id,
        ]);
    }
}
