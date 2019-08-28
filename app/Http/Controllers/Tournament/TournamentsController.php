<?php

namespace App\Http\Controllers\Tournament;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Services\Message\MessageService;
use App\Http\Requests\EditTournamentRequest;
use App\Http\Requests\CreateTournamentRequest;
use App\Services\Tournament\TournamentService;
use App\Services\Tournament\SubscribeException;
use App\Services\Tournament\TournamentNotReadyException;

class TournamentsController extends Controller
{
    /**
     * @var \App\Services\Tournament\TournamentService
     */
    protected $tournamentService;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * TournamentsController constructor.
     *
     * @param TournamentService $tournamentService
     * @param MessageService $messageService
     */
    public function __construct(TournamentService $tournamentService, MessageService $messageService)
    {
        $this->tournamentService = $tournamentService;
        $this->messageService = $messageService;
    }

    /**
     * Display the tournaments list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournaments = Tournament::all();

        return view('tournaments.index', ['tournaments' => $tournaments]);
    }

    /**
     * Inscrit une équipe
     *
     * @param \App\Models\Tournament $tournament
     * @param \App\Http\Requests\SubscribeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Tournament $tournament, SubscribeRequest $request)
    {
        try {
            $this->tournamentService->subscribe($tournament, $request->get('teamName'));

            $this->messageService->set('success', trans('layouts.tournaments.subscribe.success'));
        } catch (SubscribeException $e) {
            $this->messageService->set('danger', $e->getMessage());
        }

        return redirect()->route('tournaments.show', $tournament->id);
    }

    /**
     * Désincrit une équipe
     *
     * @param \App\Models\Tournament $tournament
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsubscribe(Tournament $tournament, Team $team)
    {
        $this->tournamentService->unsubscribe($tournament, $team);

        $this->messageService->set('success', trans('layouts.tournaments.unsubscribe.success'));

        return redirect()->back();
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
        try {
            $this->tournamentService->launch($tournament);

            $this->messageService->set('info', 'Le tournoi a été lancé.');
        } catch (TournamentNotReadyException $e) {
            $this->messageService->set('danger', 'Le tournoi ne peut être lancé, il manque des équipes ou il a déjà été lancé.');
        }

        return redirect()->route('tournaments.show', $tournament);
    }
}
