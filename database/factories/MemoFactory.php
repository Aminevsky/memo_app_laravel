<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Memo;
use Faker\Generator as Faker;

$factory->define(Memo::class, function (Faker $faker) {
    return [
        'title'     => $faker->text(255),
        'body'      => $faker->text(5000),
        'user_id'   => 1,
    ];
});
