<?php

namespace App\Mail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SinAutorizacion extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $accion;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $accion, $id)
    {
       $this->user = $user;
       $this->accion = $accion;
       $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('19170154@uttcampus.edu.mx')
        ->view('emails.sinautorizacioncorreo')
        ->with([
            'accion' => $this->accion,
        ]);
    }
}
