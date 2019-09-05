<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Support\Collection;

class ResultService
{
    /**
     * @param \App\Models\Tournament $tournament
     * @return mixed
     */
    public function getMatchs(Tournament $tournament)
    {
        $matchesByRound = $tournament->matches->groupBy('round');

        return $matchesByRound->map(function ($round) {
            $matchList = $this->getMatchesFromRound($round);

            return [
                'complete' => $this->roundIsComplete($matchList),
                'matches' => $matchList,
            ];
        });
    }

    /**
     * @param $matchList
     * @return bool
     */
    protected function roundIsComplete(Collection $matchList): bool
    {
        $status = $matchList->pluck('status')->toArray();

        return ! in_array('pending', $status,  true);
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
     * @param \App\Models\Match $match
     */
    public function setCompleteMatch(Match $match): void
    {
        $match->update([
            'status' => 'complete',
        ]);
    }

    /**
     * @param integer $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function retrieveTeam($id)
    {
        return Team::findOrFail($id);
    }

    /**
     * @param \Illuminate\Support\Collection $round
     * @return \Illuminate\Support\Collection
     */
    protected function getMatchesFromRound(Collection $round): Collection
    {
        return $round->map(function ($match) {
            $match->load('teams');
            return $match;
        });
    }
}
