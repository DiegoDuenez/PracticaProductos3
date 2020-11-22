<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    public $timestamps = false;

    protected $fillable = [
        'titulo', 'contenido', 'usuario', 'producto'
    ];

    public function usuario(){
        return $this->belongsTo(User::class);
    }
    
    public function producto(){
        return $this->belongsTo(Producto::class);
    }
}
