<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Tournament;

class TeamService
{
    /**
     * Save a team for specific tournament
     *
     * @param \App\Models\Tournament tournament
     * @param string $name
     * @return void
     */
    public function create(Tournament $tournament, $name) : void
    {
        $team = new Team([
            'name' => $name,
        ]);

        $tournament->teams()->save($team);
    }

    /**
     * Delete a team
     *
     * @param int $id
     * @return void
     */
    public function delete($id) : void
    {
        $team = Team::find($id);

        $team->delete();
    }
}
