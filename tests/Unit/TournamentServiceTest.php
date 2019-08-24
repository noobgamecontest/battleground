<?php

namespace Tests\Unit;

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
    public function can_get_all_of_the_availables_tournaments()
    {
        $service = new TournamentService();
        factory(Tournament::class, 2)->create();
        factory(Tournament::class, 3)->create([
            'started_at' => Carbon::tomorrow(),
            'ended_at' => null
        ]);

        $tournaments = $service->getAllAvailables();

        $this->assertEquals(3, $tournaments->count());
        $this->assertInstanceOf(Tournament::class, $tournaments->first());
    }

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

        $service = new TournamentService();
        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $service->subscribe($params);

        $this->assertEquals(1, $tournament->teams->count());
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Tournament\SubscribeException
     */
    public function cant_subscribe_a_team_to_a_tournament_with_existing_name()
    {
        $tournament = factory(Tournament::class)->create();
        factory(Team::class)->create([
            'name' => 'NameTest',
            'tournament_id' => $tournament->id
        ]);

        $service = new TournamentService();
        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $service->subscribe($params);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Tournament\SubscribeException
     */
    public function cant_subscribe_a_team_to_a_tournament_with_max_slots()
    {
        $tournament = factory(Tournament::class)->create(['slots' => 4]);
        factory(Team::class, 4)->create([
            'tournament_id' => $tournament->id
        ]);

        $service = new TournamentService();
        $params = [
            'tournamentId' => $tournament->id,
            'teamName' => 'NameTest',
        ];

        $service->subscribe($params);
    }
}
