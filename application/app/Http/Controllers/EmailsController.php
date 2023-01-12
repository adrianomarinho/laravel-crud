<?php

namespace App\Http\Controllers;

use App\Events\SendEmailForm;
use Illuminate\Http\Request;

class EmailsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set('America/Fortaleza');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['titulo'] = 'Certificados';

        $data['certificados'] = auth()->user()->certificados;

        $edicao = Edicoes::ano();
        if ($edicao->count()) {
            $data['edicao'] = $edicao->first();
        }

        return view('paginas.certificados.index', $data);
    }

    public function enviaEmailForm(Request $request)
    {
        /**
         * Envia email
         */
        $request->validate(
            [
                'nome' => 'required|string',
                'documento' => 'nullable|string',
                'email' => 'required|string',
                'assunto' => 'required|string',
                'mensagem' => 'required|string',
            ],
            [
                'required' => 'O :attribute é de preenchimento obrigatório'
            ]
        );

        $dados = $request->all();

        event(new SendEmailForm($dados));

        return redirect()->to('/')->withSuccess('Sua mensagem foi enviada com sucesso. Retornaremos o mais breve possível.');
    }
}
