<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Email
use Modules\Base\Http\Controllers\SendMailController;

class EnvioEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $enviar;
    protected $assunto;
    protected $conteudo;
    protected $path;
    protected $view;
    protected $tipo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($enviar, $assunto, $tipo = 'text', $view = null, $conteudo, $path)
    {
        $this->enviar = $enviar;
        $this->assunto = $assunto;
        $this->conteudo = $conteudo;
        $this->path = $path;
        $this->view = $view;
        $this->tipo = $tipo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_instancia = new SendMailController();
        $email_instancia->enviar($this->enviar, $this->assunto, $this->tipo, $this->view, $this->conteudo, $this->path);
    }
}
