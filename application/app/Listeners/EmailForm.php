<?php

namespace App\Listeners;

use App\Events\SendEmailForm;

class EmailForm
{
    /**
     * @var event
     */
//    protected $event;

    /**
     * @var emails
     */
    protected $emails;

    protected $dados;

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
    public function handle(SendEmailForm $event)
    {
        $this->event = $event;
        $this->dados = $event->getDados();
//        $this->token = $event->getToken();
        $this->sendMail();
    }

    public function sendMail()
    {

        $data = [
            'nome' => $this->dados['nome'],
            'email' => $this->dados['email'],
            'documento' => $this->dados['documento'],
            'telefone' => $this->dados['telefone'],
            'assunto' => $this->dados['assunto'],
            'mensagem' => $this->dados['mensagem'],
        ];

        /**
         * Controller de Envio de Emails
         */

        $controller = new \Modules\Base\Http\Controllers\SendMailController();

        $sendEmail = $controller->enviar(
            [config('snct.email')],
            'Contato via Site',
            'html',
            'base::emails.form',
            $data
        );

    }
}
