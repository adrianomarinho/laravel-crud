<?php

namespace App\Listeners;

use App\Events\SendEmailRegister;
use Mail;

class NovoUsuario
{
    /**
     * @var event
     */
    protected $event;

    /**
     * @var emails
     */
    protected $emails;

    /**
     * @var \App\Models\Usuarios\Usuario
     */
    protected $usuario;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emails = null;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SendEmailRegister $event)
    {
        $this->event = $event;
        $this->usuario = $event->getUsuario();
        $this->sendMail();
    }

    public function failed(NovoUsuario $event, $exception)
    {
        $this->usuario = $event->getUsuario();

        Log::notice('Falha ao enviar email de boas vindas para o usuário', [
            'id' => $this->usuario->id,
            'name' => $this->usuario->nome
        ]);
    }

    public function sendMail()
    {
        if (!$this->usuario->emails()->count()) {
            return false;
        }

        $data = [
            'nome' => $this->usuario->nome,
            'link' => route('auth.ativa-cadastro', $this->usuario->uuid),
        ];

        $emails = [];
        foreach ($this->usuario->emails()->get() as $index => $email) {

            if (!empty($email->valor)) {
                $emails[] = $email->valor;
            }
        }


        /**
         * Controller de Envio de Emails
         */

        $controller = new \Modules\Base\Http\Controllers\SendMailController();

        $sendEmail = $controller->enviar(
            $emails,
            'Ativação do Cadastro',
            'html',
            'base::emails.seja_bem_vindo',
            $data
        );

    }
}
