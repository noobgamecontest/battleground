<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Tournament;
use App\Http\Requests\AddResultMatch;
use App\Http\Requests\EditTournamentRequest;
use App\Http\Requests\CreateTournamentRequest;
use App\Services\Message\MessageService;
use App\Services\Tournament\TournamentService;

class TournamentsController extends Controller
{
    /**
     * @var \App\Services\Tournament\TournamentService
     */
    protected $service;

    /**
     * @var \App\Services\Message\MessageService
     */
    protected $messageService;

    /**
     * TournamentsController constructor.
     *
     * @param \App\Services\Tournament\TournamentService $service
     * @param \App\Services\Message\MessageService $messageService
     */
    public function __construct(TournamentService $service, MessageService $messageService)
    {
        $this->service = $service;
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tournaments.index', ['tournaments' => Tournament::all()]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $tournaments = Tournament::whereNotNull('ended_at')->get();

        return view('tournaments.history', ['tournaments' => $tournaments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tournaments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateTournamentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTournamentRequest $request)
    {
        $tournament = new Tournament($request->validated());

        $tournament->save();

        return redirect()->route('tournaments.show', $tournament);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function show(Tournament $tournament)
    {
        return view('tournaments.show', ['tournament' => $tournament]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function edit(Tournament $tournament)
    {
        return view('tournaments.edit', ['tournament' => $tournament]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EditTournamentRequest $request
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function update(EditTournamentRequest $request, Tournament $tournament)
    {
        $tournament->update($request->validated());

        return redirect()->route('tournaments.show', $tournament);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return redirect()->route('tournaments.index');
    }

    /**
     * Démarre un tournoi
     *
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Http\RedirectResponse
     */
    public function launch(Tournament $tournament)
    {
        $this->service->launch($tournament);

        return redirect()->route('tournaments.show', $tournament);
    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResults(Tournament $tournament)
    {
        $matches = $this->service->getMatchs($tournament);

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
        $this->service->setScores($request->all(), $match);

        $this->messageService->set('success', 'Le résultat a bien été ajouté');

        return redirect()->back();
    }
}
