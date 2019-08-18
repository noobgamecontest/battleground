<?php

namespace Tests\Unit;

use App\Models\Match;
use App\Models\Team;
use App\Models\Tournament;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ready_to_launch()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $this->assertTrue($tournament->readyToLaunch());
    }

    /** @test */
    public function not_ready_because_no_sufficient_teams_subscribes()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 8,
            'opponents_by_match' => 4,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 2)->make());

        $this->assertFalse($tournament->readyToLaunch());
    }

    /** @test */
    public function not_ready_because_have_matches_already()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $tournament->matches()->save(factory(Match::class)->make());

        $this->assertFalse($tournament->readyToLaunch());
    }
}
