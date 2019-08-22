<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Services\ResultService;

class ResultsController extends Controller
{
    public function getResults(Tournament $tournament)
    {
        $matches = with(new ResultService())->getMatchs($tournament);

        return view('tournaments.results', compact('matches'));
    }

    public function setResults(Request $request, Match $match)
    {
        dd($request->all());
    }
}
