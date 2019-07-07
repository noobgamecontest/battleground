<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'round', 'status',
    ];

    /**
     * The teams that belong to the match.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('score');
    }
}
