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
    public function can_launch_a_tournament_with_4_slots_and_2_teams_by_match_with_1_winner()
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

    /** @test */
    public function can_complete_a_match()
    {
        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $service = new TournamentService();

        $service->launch($tournament);

        $match = $tournament->matches()->where('round', 1)->first();

        $winner = $match->teams->first();
        $looser = $match->teams->last();

        $match->teams()->updateExistingPivot($winner, ['score' => 43]);
        $match->teams()->updateExistingPivot($looser, ['score' => 0]);

        $match->refresh();

//        dd($match->teams->pluck('pivot.score', 'name'));

        $service->completeMatch($match);

        $this->assertEquals('complete', $match->status);

    }
}
