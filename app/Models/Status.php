<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The teams that belong to the match.
     */
    public function teams()
    {
        return $this->belongsToMany(Tournament::class);
    }
}
