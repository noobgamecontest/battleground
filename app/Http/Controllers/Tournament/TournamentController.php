<?php

namespace App\Http\Controllers\Tournament;

use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Services\TournamentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Exceptions\Tournament\SubscribeException;

class TournamentController extends Controller
{
    /**
     * @var \App\Services\TournamentService
     */
    protected $tournamentService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\TournamentService $tournamentService
     * @return void
     */
    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    /**
     * Display the tournaments list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournaments = $this->tournamentService->getAllAvailables();

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
        $tournament = Tournament::find($id);

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
        $tournament = Tournament::find($request->get('tournamentId'));

        try {
            $this->tournamentService->subscribe($request->all());

            $request->session()->flash('alert-success', trans('layouts.tournaments.subscribe.success'));
            return redirect()->route('tournament.show', $tournament->id);
        } catch (SubscribeException $e) {
            $request->session()->flash('alert-danger', trans('layouts.tournaments.subscribe.error'));
            return redirect()->route('tournament.show', $tournament->id);
        }
    }

    /**
     * Delete a team from a tournament
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsubscribe(Request $request)
    {
        $this->tournamentService->unsubscribe($request->get('teamId'));

        $request->session()->flash('alert-success', trans('layouts.tournaments.unsubscribe.success'));
        return redirect()->back();
    }
}
