<?php

namespace App\Http\Controllers\Tournament;

use App\Models\Tournament;
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
}
