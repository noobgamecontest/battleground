<?php

namespace App\Services;

use App\Models\Tournament;

class ResultService
{
    /**
     * @param \App\Models\Tournament $tournament
     * @return mixed
     */
    public function getMatchs(Tournament $tournament)
    {
        $matchesByRound = $tournament->matches->groupBy('round');

        $l = $matchesByRound->map(function ($matchs) {
            return $matchs->map(function ($match) {
                $std = new \stdClass();
                $std->id = $match->id;
                $std->name = $match->name;
                $std->status = $match->status;
                $std->teams = $match->teams()->select('id', 'name')->get()->toArray();

                return $std;
            });
        });

        $st = new \stdClass();
        $st->tounamentId = $tournament->id;
        $st->rounds = $l;

        return $st;
    }
}
