<?php

namespace App\Http\Controllers\Tournament;

use Illuminate\Http\Request;
use App\Services\TeamService;
use App\Services\TournamentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;

class TournamentController extends Controller
{
    /**
     * @var \App\Services\TournamentService
     */
    protected $tournamentService;

    /**
     * @var \App\Services\TeamService
     */
    protected $teamService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\TournamentService $tournamentService
     * @param \App\Services\TeamService $teamService
     * @return void
     */
    public function __construct(TournamentService $tournamentService, TeamService $teamService)
    {
        $this->tournamentService = $tournamentService;
        $this->teamService = $teamService;
    }

    /**
     * Display the tournaments list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournaments = $this->tournamentService->getAllAvailablesTournaments();

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
        $tournament = $this->tournamentService->find($id);

        return view('tournaments.show', ['tournament' => $tournament]);
    }

    /**
     * Subscribe a team to a tournament
     *
     * @param \App\Http\Requests\SubscribeRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(SubscribeRequest $request)
    {
        $tournament = $this->tournamentService->find($request->get('tournamentId'));

        $this->teamService->create($tournament, $request->get('teamName'));

        $request->session()->flash('alert-success', trans('layouts.tournaments.subscribe.success'));
        return redirect()->route('tournament.show', $tournament->id);
    }

    /**
     * Delete a team from a tournament
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteTeam(Request $request)
    {
        $this->teamService->delete($request->get('teamId'));

        $request->session()->flash('alert-success', trans('layouts.tournaments.deleteTeam.success'));
        return redirect()->back();
    }
}
