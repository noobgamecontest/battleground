<?php

namespace Tests\Unit;


use Carbon\Carbon;
use Tests\TestCase;
use ReflectionClass;
use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Tournament\TournamentService;
use App\Services\Tournament\SubscribeException;
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

    /**
     * @test
     * @throws \ReflectionException
     */
    public function round_is_complete()
    {
        $tournament = factory(Tournament::class)->create();

        $matches = new Collection([
            factory(Match::class)->state('complete')->create([
                'tournament_id' => $tournament->id
            ]),
            factory(Match::class)->state('complete')->create([
                'tournament_id' => $tournament->id
            ]),
        ]);

        $service = new TournamentService();

        $method = $this->getMethod($service, 'roundIsComplete');

        $this->assertTrue($method->invokeArgs($service, [$matches]));
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function round_is_not_complete()
    {
        $tournament = factory(Tournament::class)->create();

        $matches = new Collection([
            factory(Match::class)->state('pending')->create([
                'tournament_id' => $tournament->id
            ]),
            factory(Match::class)->state('complete')->create([
                'tournament_id' => $tournament->id
            ]),
        ]);

        $service = new TournamentService();

        $method = $this->getMethod($service, 'roundIsComplete');

        $this->assertFalse($method->invokeArgs($service, [$matches]));
    }

    /**
     * @param string $class
     * @param string $method
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getMethod($class, $method)
    {
        $class = new ReflectionClass($class);

        $method = $class->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }
}
