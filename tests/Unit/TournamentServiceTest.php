<?php

namespace Tests\Unit;

use App\Services\Tournament\SubscribeException;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Tournament\TournamentService;
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

    /** @test */
    public function can_subscribe_a_team_to_a_tournament()
    {
        $tournament = factory(Tournament::class)->create();

        $teamName = 'Guzu3k';

        $service = new TournamentService();

        $service->subscribe($tournament, $teamName);

        $tournament->refresh();

        $this->assertCount(1, $tournament->teams);
        $this->assertEquals($teamName, $tournament->teams->first()->name);
    }

    /** @test */
    public function cant_subscribe_a_team_to_a_tournament_with_existing_name()
    {
        $this->expectException(SubscribeException::class);

        $tournament = factory(Tournament::class)->create();

        $team = factory(Team::class)->make([
            'name' => 'Nope',
        ]);

        $tournament->teams()->save($team);

        $service = new TournamentService();

        $service->subscribe($tournament, $team->name);
    }

    /** @test */
    public function cant_subscribe_a_team_to_a_tournament_with_max_slots()
    {
        $this->expectException(SubscribeException::class);

        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $teamName = 'Les bisounours du ciel';

        $service = new TournamentService();

        $service->subscribe($tournament, $teamName);
    }
}
