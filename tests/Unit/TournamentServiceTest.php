<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Models\Tournament;
use App\Services\Tournament\TournamentService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_init_a_tournament_with_4_slots_and_2_teams_by_match_with_1_winner()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $service = new TournamentService();

        $service->launch($tournament);

        $this->assertEquals(3, $tournament->matches->count());
        $this->assertEquals(1, $tournament->matches->max('round'));
        $this->assertEquals(2, $tournament->matches->get(0)->teams->count());
        $this->assertEquals(2, $tournament->matches->get(1)->teams->count());
        $this->assertEquals(0, $tournament->matches->get(2)->teams->count());
    }
}
