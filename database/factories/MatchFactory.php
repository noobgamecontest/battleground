<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Match;
use Faker\Generator as Faker;

$factory->define(Match::class, function (Faker $faker) {
    return [
        'round' => 3,
        'name' => $faker->bankAccountNumber,
        'tournament_id' => null
    ];
});

$factory->state(Match::class, 'pending', function () {
   return [
       'status' => 'pending',
   ];
});

$factory->state(Match::class, 'complete', function () {
    return [
        'status' => 'complete',
    ];
});

