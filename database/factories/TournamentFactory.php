<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Team;
use App\Models\Tournament;
use Faker\Generator as Faker;
use App\Services\Tournament\TournamentService;

$factory->define(Tournament::class, function (Faker $faker) {
    return [
        'name' => 'NGC #' . $faker->randomDigit,
        'started_at' => $faker->dateTime,
        'ended_at' => function (array $tournament) use ($faker) {
            return $faker->dateTimeBetween($tournament['started_at']);
        },
        'slots' => 16,
        'opponents_by_match' => 4,
        'winners_by_match' => 2,
    ];
});

$factory->state(Tournament::class, 'versus', function () {
    return [
        'opponents_by_match' => 2,
        'winners_by_match' => 1,
    ];
});

$factory->state(Tournament::class, 'launched', function () {
    return [
        //
    ];
});

$factory->afterCreatingState(Tournament::class, 'launched', function ($tournament, $faker) {
    $tournament->teams()->saveMany(factory(Team::class, $tournament->slots)->make());
    app(TournamentService::class)->launch($tournament);
});
