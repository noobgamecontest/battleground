<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'registrated_at',
    ];

    /**
     * Get the tournament for the registration.
     */
    public function tournament()
    {
        return $this->hasOne(Tournament::class);
    }
}
