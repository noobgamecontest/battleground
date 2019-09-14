<?php

use App\Models\Status;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Tournament::class)->create(['name' => 'Smash Bros']);
        factory(Tournament::class)->create(['name' => 'Mario Kart']);
        factory(Tournament::class)->create(['name' => 'Just Dance']);
        factory(Tournament::class)->create(['name' => 'Fifa 19']);

        factory(Status::class)->create(['name' => 'CREATED']);
        factory(Status::class)->create(['name' => 'STARTED']);
        factory(Status::class)->create(['name' => 'ENDED']);
    }
}
