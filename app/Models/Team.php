<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'position'
    ];

    /**
     * The matches that belong to the team.
     */
    public function matches()
    {
        return $this->belongsToMany(Match::class)->withPivot('score');
    }

    /**
     * The tournament that belong to the team.
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
