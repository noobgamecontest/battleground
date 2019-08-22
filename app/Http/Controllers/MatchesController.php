<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Services\Tournament\TournamentService;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    /**
     * @var \App\Services\Tournament\TournamentService
     */
    protected $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Match  $match
     * @return \Illuminate\Http\Response
     */
    public function complete(Match $match)
    {
        $this->tournamentService->closeMatch($match);

        return redirect('tournaments.index', $match->tournament);
    }
}
