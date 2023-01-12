<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailResetPassword
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Usuario
     */
    private $usuario;

    /**
     * @var Token
     */
    private $token;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($usuario, $token)
    {
        $this->usuario = $usuario;
        $this->token = $token;
    }

    /**
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
