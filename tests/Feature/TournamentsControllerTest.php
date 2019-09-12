<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\Match;
use App\Models\Tournament;
use App\Services\Message\Message;
use Illuminate\Foundation\Testing\WithFaker;
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

    /** @test */
    public function user_can_subscribe_to_a_specific_tournament()
    {
        $user = factory(User::class)->create();
        $tournament = factory(Tournament::class)->create(['slots' => 5]);

        $params = [
            'teamName' => 'Yolo Swag',
        ];

        $response = $this->actingAs($user)->post('tournaments/' . $tournament->id . '/subscribe', $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('teams', [
            'name' => $params['teamName'],
            'tournament_id' => $tournament->id,
        ]);
    }

    /** @test */
    public function user_cant_subscribe_to_a_specific_tournament_with_existing_team_name()
    {
        $user = factory(User::class)->create();
        $tournament = factory(Tournament::class)->create(['slots' => 4]);

        $existingTeam = factory(Team::class)->make([
            'name' => 'Already take',
        ]);

        $tournament->teams()->save($existingTeam);

        $params = [
            'teamName' => $existingTeam->name,
        ];

        $response = $this->actingAs($user)->post('tournaments/' . $tournament->id . '/subscribe', $params);
        $response->assertRedirect('tournaments/'. $tournament->id);

        $message = new Message('danger', "Ce nom d'équipe existe déjà");
        $response->assertSessionHas(['message' => $message]);
    }

    /** @test */
    public function user_cant_subscribe_to_a_specific_tournament_with_max_slots()
    {
        $user = factory(User::class)->create();

        $tournament = factory(Tournament::class)->create([
            'slots' => 4,
            'opponents_by_match' => 2,
            'winners_by_match' => 1,
        ]);

        $tournament->teams()->saveMany(factory(Team::class, 4)->make());

        $params = [
            'teamName' => 'NameTest',
        ];

        $response = $this->actingAs($user)->post('tournaments/' . $tournament->id . '/subscribe', $params);
        $response->assertRedirect('tournaments/'. $tournament->id);

        $message = new Message('danger', "Toutes les places sont déjà occupées pour ce tournois");
        $response->assertSessionHas(['message' => $message]);
    }

    /** @test */
    public function an_admin_can_delete_team()
    {
        $admin = factory(User::class)->state('admin')->create();
        $tournament = factory(Tournament::class)->create(['slots' => 4]);

        $team = factory(Team::class)->make([
            'name' => 'Already take',
        ]);

        $tournament->teams()->save($team);

        $response = $this->actingAs($admin)->patch('tournaments/' . $tournament->id . '/unsubscribe/' . $team->id);
        $response->assertStatus(302);

        $message = new Message('success', "L'équipe a été désinscrite avec succès");
        $response->assertSessionHas(['message' => $message]);

        $this->assertDatabaseMissing('teams', [
            'name' => $team->name,
            'tournament_id' => $tournament->id,
        ]);
    }

    /**
     * @test
     */
    public function user_cant_write_scores()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $tournament = $this->makeTournament();

        $randomMatch = $this->getRandomMatchFromFirstRound($tournament);

        $scores = $this->generateScore($randomMatch);

        $response = $this->post(route('tournaments.results.post', $randomMatch), ['teams' => $scores]);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function admin_can_add_scores()
    {
        $admin = factory(User::class)->state('admin')->create();

        $this->actingAs($admin);

        $tournament = $this->makeTournament();

        $randomMatch = $this->getRandomMatchFromFirstRound($tournament);

        $this->assertEquals('pending', $randomMatch->status);

        $scores = $this->generateScore($randomMatch);
        $response = $this->post(route('tournaments.results.post', $randomMatch), ['teams' => $scores]);
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
    public function throw_exception_when_team_not_exist_in_match()
    {
        $admin = factory(User::class)->state('admin')->create();

        $this->actingAs($admin);

        $tournament = $this->makeTournament();

        $randomMatch = $this->getRandomMatchFromFirstRound($tournament);

        $this->assertEquals('pending', $randomMatch->status);

        $scores = [
            '53' => 20,
            '10' => 58
        ];

        try {
            $this->post(route('tournaments.results.post', $randomMatch), ['teams' => $scores]);
        } catch (\Exception $e) {
            $this->assertEquals('Score team not found', $e->getMessage());
        }
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
