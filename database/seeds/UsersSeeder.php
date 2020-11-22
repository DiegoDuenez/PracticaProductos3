<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 19)->create();

        App\User::create([ //USUARIO ADMINISTRADOR

            'name' => 'Diego',
            'years_old' => 18,
            'email' => '19170154@uttcampus.edu.mx',
            'rol' => 'admin',
            'password' => bcrypt('123'),
            'imagen' => 'sin imagen',
            'estado'=> 1,
            'codigo_act' => Str::random(10),

        ]);
    }
}
