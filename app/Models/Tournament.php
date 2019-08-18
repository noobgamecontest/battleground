<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'started_at', 'ended_at', 'slots', 'opponents_by_match', 'winners_by_match',
    ];

    /**
     * Get the teams for the tournament.
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the matches for the tournament.
     */
    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    /**
     * Détermine si un tournoi peut être lancé
     *
     * @return bool
     */
    public function readyToLaunch() : bool
    {
        return $this->teams()->count() && ! $this->matches()->count();
    }
}
