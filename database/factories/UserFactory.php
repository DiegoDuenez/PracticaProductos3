<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name'=> $faker->text(10),
        'years_old'=> rand(18,50),
        'email'=> $faker->unique()->safeEmail,
        'rol' => 'user',
        'password' => bcrypt('123'),
        'imagen' => 'sin imagen',
        'estado' => 0,
        'codigo_act' => Str::random(10),
        
    ];
});
