<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Tournament;
use App\Services\ResultService;
use App\Http\Requests\AddResultMatch;
use App\Services\Message\MessageService;

class ResultsController extends Controller
{
    /**
     * @var \App\Services\ResultService
     */
    protected $resultService;

    /**
     * @var \App\Services\Message\MessageService
     */
    protected $messageService;

    /**
     * ResultsController constructor.
     * @param \App\Services\ResultService $resultService
     * @param \App\Services\Message\MessageService $messageService
     */
    public function __construct(ResultService $resultService, MessageService $messageService)
    {
        $this->resultService = $resultService;
        $this->messageService = $messageService;
    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResults(Tournament $tournament)
    {
        $matches = $this->resultService->getMatchs($tournament);

        return view('tournaments.results', compact('matches', 'tournament'));
    }

    /**
     * @param \App\Http\Requests\AddResultMatch $request
     * @param \App\Models\Match $match
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Services\Message\UnexpectedMessageTypeException
     */
    public function setResults(AddResultMatch $request, Match $match)
    {
        $this->resultService->setScores($request->all(), $match);

        $this->resultService->setCompleteMatch($match);

        $this->messageService->set('success', 'Le résultat a bien été ajouté');

        return redirect()->back();
    }
}
