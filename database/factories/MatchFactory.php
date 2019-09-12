<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Match;
use Faker\Generator as Faker;

$factory->define(Match::class, function (Faker $faker) {
    return [
        'round' => $faker->randomDigit,
        'name' => 'Fight ' . $faker->colorName,
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
