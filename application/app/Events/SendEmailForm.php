<?php

namespace App\Events;

use App\Models\Usuarios\Usuario;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailForm
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Usuario
     */
    private $dados;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return Usuario
     */
    public function getDados()
    {
        return $this->dados;
    }
}
