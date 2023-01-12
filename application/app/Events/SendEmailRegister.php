<?php

namespace App\Events;

use App\Models\Usuarios\Usuario;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailRegister
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Usuario
     */
    private $usuario;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
