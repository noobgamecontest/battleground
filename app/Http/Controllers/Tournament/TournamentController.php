<?php

namespace App\Http\Controllers\Tournament;

use Illuminate\Http\Request;
use App\Services\TournamentService;
use App\Http\Controllers\Controller;

class TournamentController extends Controller
{
    /**
     * @var \App\Services\TournamentService
     */
    protected $service;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\TournamentService $service
     * @return void
     */
    public function __construct(TournamentService $service)
    {
        $this->middleware('guest');
        $this->service = $service;
    }

    /**
     * Display the tournaments list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournaments = $this->service->getAllAvailablesTournaments();

        return view('tournaments.index', ['tournaments' => $tournaments]);
    }

    /**
     * Display a specific tournament
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $tournament = $this->service->find($id);

        return view('tournaments.show', ['tournament' => $tournament]);
    }

    /**
     * Subscribe a team to a tournament
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request, $id)
    {
        $tournament = $this->service->find($id);

        $this->service->createTeam($tournament, $request->get('teamName'));

        $request->session()->flash('alert-success', trans('layouts.tournaments.subscribe.success'));
        return redirect()->route('tournament.show', $tournament->id);
    }
}
