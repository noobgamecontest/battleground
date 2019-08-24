<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\Tournament\SubscribeException;

class TournamentService
{
    /**
     * Return all availables tournaments
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllAvailables() : Collection
    {
        return Tournament::where('started_at', '>', Carbon::now())
            ->whereNull('ended_at')
            ->get();
    }

    /**
     * Inscriptions d'une équipe à un tournoi
     *
     * @param array $attrs
     * @throws \App\Exceptions\Tournament\SubscribeException
     * @return void
     */
    public function subscribe(array $attrs) : void
    {
        $tournament = Tournament::find($attrs['tournamentId']);
        $target = $tournament->teams->where('name', $attrs['teamName']);

        if ($target->isNotEmpty()) {
            throw new SubscribeException();
        }

        $team = new Team([
            'name' => $attrs['teamName'],
        ]);

        $tournament->teams()->save($team);
    }

    /**
     * Supprime une équipe d'un tournoi
     *
     * @param $id
     * @return void
     */
    public function unsubscribe($id) : void
    {
        $team = Team::find($id);

        $team->delete();
    }
}
