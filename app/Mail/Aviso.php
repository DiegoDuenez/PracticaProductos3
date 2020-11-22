<?php

namespace App\Mail;
use App\User;
use App\Modelos\Producto;
use App\Modelos\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Aviso extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $prod;
    public $accion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $prod, $accion)
    {
        $this->user = $user;
        $this->prod = $prod;
        $this->accion = $accion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('19170154@uttcampus.edu.mx')
        ->view('emails.aviso')
        ->with([
            'accion' => $this->accion,
        ]);
    }
}
