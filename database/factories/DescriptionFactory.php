<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Description::class, function (Faker $faker) {
    static $password;

    return [
        'title' => $faker->word,
        'description' => $faker->paragraph,
        'user_id' => function() { return factory(\App\User::class)->create()->id; },
    ];
});
