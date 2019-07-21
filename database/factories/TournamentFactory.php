<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Carbon\Carbon;
use App\Models\Tournament;
use Faker\Generator as Faker;

$factory->define(Tournament::class, function (Faker $faker) {
    return [
        'name' => $faker->userName,
        'started_at' => Carbon::now(),
        'ended_at' => Carbon::tomorrow(),
    ];
});
