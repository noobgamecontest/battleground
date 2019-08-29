<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Match;
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

        return $matchesByRound->map(function ($matchs) {
            return $matchs->map(function ($match) {
                $match->load('teams');
                return $match;
            });
        });
    }

    /**
     * @param array $data
     * @param \App\Models\Match $match
     */
    public function setScores(array $data, Match $match)
    {
        $scores = $data['teams'];

        foreach ($scores as $teamId => $score) {
            $team = $this->retrieveTeam($teamId);

            $match->teams()->updateExistingPivot($team, ['score' => (int) $scores[$team->id]]);
        }
    }

    /**
     * @param integer $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function retrieveTeam($id)
    {
        return Team::findOrFail($id);
    }
}
