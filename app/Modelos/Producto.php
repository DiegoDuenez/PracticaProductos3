<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    public $timestamps = false;

    protected $fillable = [
        'nombre', 'precio', 'usuario'
    ];

    public function comentarios(){
        return $this->hasMany(Comentario::class);
    }

    public function usuario(){
        return $this->belongsToy(User::class);
    }


}
