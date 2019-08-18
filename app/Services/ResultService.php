<?php

namespace App\Services;

use App\Models\Tournament;

class Bag
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $bag;

    public function __construct($round)
    {

    }
}

class ResultService
{
    public function getMatchs()
    {
        $tournement = $this->getTrounament();

        $matchesByRound = $tournement->matches->groupBy('round');

        return $matchesByRound->map(function ($matchs) {

            return $matchs->map(function ($match) {
                $std = new \stdClass();
                $std->name = $match->name;
                $std->type = 'versus';
                $std->status = $match->status;
                $std->teams = $match->teams()->select('id', 'name')->get()->toArray();

                return $std;
            });
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static|static[]
     */
    protected function getTrounament()
    {
        return Tournament::with('matches')->findOrFail(1);
    }
}
