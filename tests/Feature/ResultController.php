<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use App\Services\ResultService;
use App\Services\Tournament\TournamentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResultController extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function cant_add_score_from_match_with_user_basic()
    {
        $this->makeAndActingAsUser();

        $tournament = $this->makeTournament();

        $randomMatch = $this->getRandomMatchFromFirstRound($tournament);

        $scores = $this->generateScore($randomMatch);

        $response = $this->post(route('results.post', $randomMatch), ['teams' => $scores]);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function can_add_score_from_match()
    {
        $this->makeAndActingAsAdmin();

        $tournament = $this->makeTournament();

        $randomMatch = $this->getRandomMatchFromFirstRound($tournament);

        $this->assertEquals('pending', $randomMatch->status);

        $scores = $this->generateScore($randomMatch);
        $response = $this->post(route('results.post', $randomMatch), ['teams' => $scores]);
        $response->assertSessionHas('message');

        $randomMatch->refresh();

        $this->assertEquals('complete', $randomMatch->status);

        foreach ($scores as $teamId => $score) {
            $this->assertDatabaseHas('match_team', [
                'match_id' => $randomMatch->id,
                'team_id' => $teamId,
                'score' => $score,
            ]);
        }
    }

    /**
     * @test
     */
    public function round_is_complete_when_all_result_is_set()
    {
        $this->makeAndActingAsAdmin();

        $tournament = $this->makeTournament();

        $round = $this->getFirstRound($tournament);

        $this->assertFalse($round['complete']);

        foreach ($round['matches'] as $match) {
            $scores = $this->generateScore($match);
            $this->post(route('results.post', $match), ['teams' => $scores]);
        }

        $tournament->refresh();

        $round = $this->getFirstRound($tournament);

        $this->assertTrue($round['complete']);
    }

    /**
     * @return \App\Models\Tournament
     */
    protected function makeTournament()
    {
        $tournament = factory(Tournament::class)->state('versus')->create();

        $tournament->teams()->saveMany(factory(Team::class, 16)->make());

        $service = new TournamentService();

        $service->launch($tournament);

        return $tournament;
    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return mixed
     */
    public function getFirstRound(Tournament $tournament)
    {
        $resultService = new ResultService();

        $matches = $resultService->getMatchs($tournament);

        return  $matches->first();

    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return \App\Models\Match
     */
    protected function getRandomMatchFromFirstRound(Tournament $tournament)
    {
        $round = $this->getFirstRound($tournament);

        return $round['matches']->random();
    }

    /**
     * @return void
     */
    public function makeAndActingAsUser()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);
    }

    /**
     * @return void
     */
    protected function makeAndActingAsAdmin()
    {
        $admin = factory(User::class)->state('admin')->create();
        $this->actingAs($admin);
    }


    /**
     * @param \App\Models\Match $match
     * @return array
     */
    protected function generateScore(Match $match)
    {
        $parameters = [];

        $idList = $match->teams->pluck('id');

        foreach ($idList as $id) {
            $parameters[$id] = rand(0, 50);
        }

        return $parameters;
    }
}
