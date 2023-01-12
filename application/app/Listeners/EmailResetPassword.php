<?php

namespace App\Listeners;

use App\Events\SendEmailResetPassword;

class EmailResetPassword
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
    public function handle(SendEmailResetPassword $event)
    {
        $this->event = $event;
        $this->usuario = $event->getUsuario();
        $this->token = $event->getToken();
        $this->sendMail();
    }

    public function sendMail()
    {
        if (!$this->usuario->emails()->count()) {
            return false;
        }

        $data = [
            'nome' => $this->usuario->nome,
            'link' => route('password.reset', $this->token),
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
            'Reset de Senha',
            'html',
            'base::emails.reset_senha',
            $data
        );

    }
}
