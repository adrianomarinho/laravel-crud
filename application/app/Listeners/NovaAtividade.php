<?php

namespace App\Listeners;

class NovaAtividade
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
    public function handle(\App\Events\NovaAtividade $event)
    {
        $this->event = $event;
        $this->atividade = $event->getAtividade();
        $this->sendMail();
    }

    public function sendMail()
    {
        $usuario = $this->atividade->usuario;
        if(!$usuario){
            return false;
        }

        if (!$usuario->emails()->count()) {
            return false;
        }

        $data = [
            'nome' => $usuario->nome,
            'atividade' => [
                'Classe do Proponente' => $this->atividade->proponente->descricao,
                'Tipo de Atividade' => $this->atividade->tipo->descricao,
                'Área de Conhecimento' => $this->atividade->descricaoArea()
            ]
        ];

        foreach ($this->atividade->metadados()->where('chave', '<>', 'resumo')->get() as $key => $value) {
              $data['metadados'][strtoupper($value->chave)] = $value->valor;
        }

        foreach ($this->atividade->participantes()->get() as $kp => $vp){
            $data['participantes'][] = [
                'nome' => $vp->usuarios->nome,
                'tipo' => $vp->tipo,
                'instituicao' => $vp->instituicao,
            ];
        }

        $emails = [];
        foreach ($usuario->emails()->get() as $index => $email) {

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
            'Cadastro de Atividade',
            'html',
            'base::emails.confirmacao_inscricao_atividade',
            $data
        );

    }

}
