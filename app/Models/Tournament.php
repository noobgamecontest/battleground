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
        'name', 'started_at', 'ended_at',
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
}
