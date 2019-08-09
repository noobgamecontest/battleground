<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Http\Requests\EditTournamentRequest;
use App\Http\Requests\CreateTournamentRequest;

class TournamentsController extends Controller
{
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
}
