<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use App\Services\Tournament\TournamentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_list_tournaments()
    {
        $tournaments = factory(Tournament::class, 2)->create();

        $response = $this->get('/');

        $response->assertSeeText($tournaments->first()->name);
        $response->assertSeeText($tournaments->last()->name);
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_show_one()
    {
        $admin = factory(User::class)->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->get('tournaments/' . $tournament->id);

        $response->assertSeeText($tournament->name);
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_one()
    {
        $admin = factory(User::class)->state('admin')->create();

        $response = $this->actingAs($admin)->get('tournaments/create');

        $response->assertSeeText("Création d'un tournoi");
        $response->assertStatus(200);
        
        $properties = [
            'name' => 'Easy Contest',
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,            
        ];

        $response = $this->actingAs($admin)->post('tournaments', $properties);
        
        $response->assertRedirect('tournaments/1');
        
        $this->assertDatabaseHas('tournaments', $properties);
    }

    /** @test */
    public function admin_can_update_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->get('tournaments/' . $tournament->id . '/edit');

        $response->assertSeeText("Edition d'un tournoi");
        $response->assertStatus(200);

        $properties = [
            'name' => 'Easy Contest',
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => null,
        ];

        $response = $this->actingAs($admin)->put('tournaments/' . $tournament->id, $properties);

        $response->assertRedirect('tournaments/' . $tournament->id);

        $this->assertDatabaseHas('tournaments', ['id' => $tournament->id] + $properties);
    }

    /** @test */
    public function admin_can_delete_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create();

        $response = $this->actingAs($admin)->delete('tournaments/' . $tournament->id);

        $response->assertRedirect('/');

        $this->assertDatabaseMissing('tournaments', ['id' => $tournament->id]);
    }

    /** @test */
    public function admin_can_launch_one()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create([
            'name' => 'NGC #49',
            'slots' => 16,
            'opponents_by_match' => 4,
            'winners_by_match' => 2,
        ]);
        $tournament->teams()->saveMany(factory(Team::class, 15)->make());

        $response = $this->actingAs($admin)->patch('tournaments/' . $tournament->id . '/launch');

        $response->assertRedirect('tournaments/'. $tournament->id);

        $this->assertDatabaseHas('matches', ['tournament_id' => $tournament->id]);
    }

    /**
     * @test
     */
    public function user_cant_write_scores()
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
    public function round_is_complete_when_all_results_are_set()
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
    protected function getFirstRound(Tournament $tournament)
    {
        $resultService = new TournamentService();

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
    protected function makeAndActingAsUser()
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
