<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modelos\Comentario;
use Faker\Generator as Faker;

$factory->define(Comentario::class, function (Faker $faker) {
    return [
        'titulo'=> $faker->text(10),
        'contenido'=> $faker->text(10),
        'usuario'=> rand(1,19),
        'producto'=> rand(1, 25),
    ];
});
