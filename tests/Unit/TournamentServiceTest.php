<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tournament;
use App\Services\TournamentService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Services\TournamentService $service
     */
    protected $service;

    /**
     * Setting up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new TournamentService();
    }

    /** @test */
    public function canFindSpecificTournament()
    {
        $tournament = factory(Tournament::class)->create();

        $find = $this->service->find($tournament->id);

        $this->assertInstanceOf(Tournament::class, $find);
        $this->assertEquals($find->id, $tournament->id);
    }

    /** @test */
    public function canGetAllAvailableTournament()
    {
        factory(Tournament::class, 5)->create();

        $tournaments = $this->service->getAllAvailables();

        $this->assertNotEmpty($tournaments);
        $this->assertInstanceOf(Tournament::class, $tournaments->first());
    }
}
