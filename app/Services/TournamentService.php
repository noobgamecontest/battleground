<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;

class TournamentService
{
    /**
     * Return all availables tournaments
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllAvailablesTournaments() : Collection
    {
        return Tournament::where('started_at', '>', Carbon::now())
            ->whereNull('ended_at')
            ->get();
    }
}
