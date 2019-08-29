<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Services\ResultService;

class ResultsController extends Controller
{
    /**
     * @var \App\Services\ResultService
     */
    protected $resultService;

    /**
     * ResultsController constructor.
     * @param \App\Services\ResultService $resultService
     */
    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResults(Tournament $tournament)
    {
        $matches = $this->resultService->getMatchs($tournament);

        return view('tournaments.results', compact('matches'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Match $match
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setResults(Request $request, Match $match)
    {
        $this->resultService->setScores($request->all(), $match);

        return redirect()->back();
    }
}
