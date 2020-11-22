<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modelos\Producto;
use Faker\Generator as Faker;

$factory->define(Producto::class, function (Faker $faker) {
    return [
        'nombre'=> $faker->text(10),
        'precio'=> rand(18,50),
        'usuario'=> rand(1,19),
    ];
});
