<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\Tournament\SubscribeException;

class TournamentService
{
    /**
     * Return a tournament
     *
     * @param int $id
     * @return \App\Models\Tournament
     */
    public function find($id) : Tournament
    {
        return Tournament::find($id);
    }

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

    public function subscribe(array $attrs)
    {
        $tournament = Tournament::find($attrs['tournamentId']);
        $target = $tournament->teams->where('name', $attrs['teamName']);

        if ($target->isNotEmpty()) {
            throw new SubscribeException();
        }

        
    }

    public function unsubscribe()
    {

    }
}
