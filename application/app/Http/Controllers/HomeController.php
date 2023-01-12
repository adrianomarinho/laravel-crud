<?php

namespace App\Http\Controllers;

use App\Models\Inscricoes\Atividade;
use App\Events\NovaAtividade;
use App\Models\Metadados;
use App\Models\Inscricoes\AreaSub;
use App\Models\Inscricoes\Atividade_Usuarios;
use App\Models\Inscricoes\Proponente;
use App\Models\Programacao\Programacao;
use App\Models\Eventos\Edicoes;
use App\Models\Eventos\Edicoes_Atividades_Tipos;
use App\Models\Usuarios\Usuario;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Base\Http\Controllers\StorageController;
use \Validator;

class HomeController extends Controller
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
//        $edicao = Edicoes::ano();
//        if ($edicao->count()) {
//            $data['edicao'] = $edicao->first();
//        } else {
//            $data['edicao'] = Edicoes::orderBy('id', 'DESC')->first();
//        }
//
//        $edicao_atividade_tipo_id = Edicoes_Atividades_Tipos::where('edicao_id', $data['edicao']->id)->where('atividade_tipo_id', 1)->first()->id;
//        $data['programacao_dias'] = $this->diasProgramacao($data['edicao']);
//
//        /**
//         * Verifica se existe transmissão ao vivo
//         */
//        $transmissao = $this->verificaTransmissao($data['edicao']);
//
//        $link  = "bphtZvZfEBk";
////        $banner = asset('/banner_assista.png');
//
//        if($transmissao)
//        {
//            // $data_hora = explode('/', $transmissao['data']);
//            // $data_hora = $data_hora[2] .'-'. $data_hora[1] .'-'. $data_hora[0];
//            // $programacao_banner = Programacao::where('dia', $data_hora);
//
//            // if($transmissao['horario'])
//            // {
//            //     $hora = explode(' ', $transmissao['horario']);
//
//            //     $programacao_banner = $programacao_banner->whereTime('hora_inicio', '<=', $hora[0].':00')->whereTime('hora_fim', '<=', $hora[1].':00');
//            // }
//
//            // foreach ($programacao_banner->get() as $banner_instance)
//            // {
//            //     $meta = $banner_instance->arquivos()->first();
//            //     if (!$meta)
//            //         continue;
//
//            //     $banner =  asset('programacao/'.$banner_instance->id.'/'.$meta->nome);
//            //     break;
//            // }
//
//            $link = $transmissao['link'];
//            $data['link_completo'] = $transmissao['link'];
//            $data['transmissao'] = $transmissao;
//
//        }
//         else {
//
////             $hoje = Carbon::now()->format('Y-m-d');
////             $horaAtual = Carbon::now()->format('H:i:s');
////
////             $programacao_banner = Programacao::where('dia', $hoje);
////
////             $primeira = $programacao_banner->first();
////             if($primeira->hora_inicio <= $horaAtual){
////                 if(file_exists(public_path() . '/programacao/'.$primeira->id. '/' . $primeira->banner)){
////                     $banner =  asset('programacao/'.$primeira->id.'/'.$primeira->banner);
////                 }
////             }
//
//         }
//
//
//        $data['link'] = str_replace('https://youtu.be/', '', $link);
//
////        $data['divulgacaoResultado'] = $this->divulgacaoResultado($data['edicao'], 'programacao');
//
//        $data['prazos'] = [
//            'inscricoes' => $this->verificaPrazoDeInscricoes(),
//            'programacao' => $this->divulgacaoResultado($data['edicao'], 'programacao'),
//            'resultado' => $this->divulgacaoResultado($data['edicao'], 'resultado'),
//        ];
//
////        $data['banner'] = $banner;
//
//        $atividades_id = Atividade::where('edicao_atividade_tipo_id', $edicao_atividade_tipo_id)
//            ->where('status_id', 3)
//            ->pluck('id')->toArray();
//
//        $programacao_posters = Programacao::whereIn('atividade_id', $atividades_id)->get();
//
//        $data['posters'] = [];
//
//        foreach ($programacao_posters as $poster) {
//            $ativ_dados = $poster->atividades()->first();
//
//            $participantes = $ativ_dados->participantes()->get();
//            $data['posters'][$poster->atividade_id]['participantes'] = '';
//            foreach ($participantes as $participante) {
//                $data['posters'][$poster->atividade_id]['participantes'] .= '<strong>' . $participante->usuarios()->first()->nome . '</strong> - ' .
//                    $participante->instituicao . ' / ';
//            }
//
//            $dados = Metadados::where('entidade_type', 'App\Atividades\Atividade')
//                ->where('chave', 'titulo')
//                ->whereIn('entidade_id', $atividades_id)
//                ->orderBy('valor')
//                ->pluck('valor', 'entidade_id')->toArray();
//
//            $data['posters'][$poster->atividade_id]['titulo'] = $dados[$poster->atividade_id];
//            $data['posters'][$poster->atividade_id]['link'] = $poster->link ? $poster->link : 0;
//        }
//
//        return view('welcome', $data);
        return view('manutencao');
    }

    public function diasProgramacao($edicao)
    {
        $dias = Programacao::where('edicao_id', $edicao->id)->distinct()->orderBy('dia')->get(['dia']);

        return $dias;
    }

    public function dadosProgramacao(Request $request)
    {
        $edicao_id = $request->get('edicao_id');
        $dia = $request->get('dia');

        $programacao = Programacao::where('edicao_id', $edicao_id)->where('dia', $dia)->orderBy('hora_inicio')->get();

        $dados = [];
        $atividade_cor = [];

        foreach ($programacao as $key => $progra) {
            $atividade = $progra->atividades()->first();
            $participantes = $atividade->participantes()->get();
            $tipo = $atividade->tipos()->first()->tipos()->first();

            if (in_array($tipo->id, [1]))
                continue;

            $nomesParticipantes = '';
            $arrayParticipantes = [];

            foreach ($participantes as $participante) {

                if(in_array($participante->usuario_id, $arrayParticipantes))
                    continue;

                array_push($arrayParticipantes, $participante->usuario_id);

                if(!empty($participante->instituicao)){
                    $nomesParticipantes .= '<strong style="text-transform: uppercase">' . $participante->usuarios()->first()->nome . '</strong> - ' .
                        $participante->instituicao . ' / ';
                } else {
                    $nomesParticipantes .= '<strong style="text-transform: uppercase">' . $participante->usuarios()->first()->nome . '</strong> / ';
                }
                
            }

            $dados[$tipo->descricao][$key]['participantes'] = substr($nomesParticipantes, 0, -2);

            $dados[$tipo->descricao][$key]['horario'] = $progra->hora_inicio . ' ás ' . $progra->hora_fim;
            $dados[$tipo->descricao][$key]['titulo'] = $atividade->metadados()->where('chave', 'titulo')->first()->valor;

            $atividade_cor[$tipo->descricao] = $tipo->cor;
        }

        return response()->json(['msg' => 'ok', 'dados' => $dados, 'cores' => $atividade_cor], 200);
    }

    public function registro()
    {
        //        $data['proponentes'] = Proponente::where('status_id', 1)
        //            ->orderBy('descricao')
        //            ->get();
        //
        //        $data['atividades_tipo'] = Atividade_Tipo::where('status_id', 1)
        //            ->orderBy('descricao')
        //            ->get();
        //
        //        $data['area_sub'] = AreaSub::orderBy('area')->get();
        //
        //        $edicao = Edicoes::ano();
        //        if($edicao->count()){
        //            $data['edicao']  = $edicao->first();
        //        }
        //
        //        if (Auth::user()) {
        //            $instanceAtividade = Atividade::where('usuario_id', auth()->user()->id)
        //                ->where('usuario_type', 'usuarios')
        //                ->where('status_id', 1);
        //            if ($instanceAtividade->count()) {
        //                $data['atividades'] = $instanceAtividade->get();
        //            }
        //
        //        }

        $edicao = Edicoes::ano();
        if ($edicao->count()) {
            $data['edicao'] = $edicao->first();
        } else {
            $data['edicao'] = Edicoes::orderBy('id', 'DESC')->first();
        }

        return view('auth.register', $data);
    }

    public function atividades()
    {

        $data['edicao'] = [];
        $data['edicao_atividade_tipo_id'] = [];

        $edicao = Edicoes::ano();
        if ($edicao->count()) {

            $data['edicao'] = $edicao->first();
            $data['edicao_atividade_tipo_id'] = Edicoes_Atividades_Tipos::where('edicao_id', $edicao->first()->id)
                ->get();
        }

        $data['proponentes'] = Proponente::where('status_id', 1)
            ->orderBy('descricao')
            ->get();

        $data['area_sub'] = AreaSub::orderBy('area')->get();

        $data['inscricoes'] = $this->verificaPrazoDeInscricoes();

        if (Auth::user()) {
            $instanceAtividade = Atividade::where('usuario_id', auth()->user()->id)
                ->where('usuario_type', 'usuarios')
                ->where('status_id', '<>', 2);
            if ($instanceAtividade->count()) {
                $data['atividades'] = $instanceAtividade->get();
            }
        }

        $data['prazos'] = [
            'inscricoes' => $this->verificaPrazoDeInscricoes(),
            'programacao' => $this->divulgacaoResultado($data['edicao'], 'programacao'),
            'resultado' => $this->divulgacaoResultado($data['edicao'], 'resultado'),
        ];

        return view('paginas.atividades.cadastrar', $data);
    }

    public function createAtividade(Request $request)
    {

        /**
         * Verifica Prazo de Inscricoes
         */
        if (!$this->verificaPrazoDeInscricoes()) {
            return back()->withErrors('O prazo para inscrições já está encerrado.');
        }

        /**
         * Valida dados
         * @todo Criar Form Request
         */
        if (!$request->has('atividade')) {
            return back()->withErrors('Dados incorretos. Por favor, tente novamente.');
        }

        $dados = [];
        $metadados = [];

        /**
         * Edição da Semana
         * @todo Organizar para buscar sempre a mais atual ou a ativa
         */
        $dados['edicao_id'] = Edicoes::orderBy('id', 'DESC')->first()->id;

        /**
         * Valida Classe do Proponente
         */
        $instanceProponente = Proponente::class;

        if (!$proponente = $instanceProponente::where('id', $request->get('atividade')['classe'])->where('status_id', 1)->first()) {
            return back()->withErrors('Classe do Proponente não encontrada. Por favor, tente novamente.');
        }

        $dados['proponente_tipo_id'] = $proponente->id;

        /**
         * Valida Tipo de Atividade
         */

        $instanceTipo = Edicoes_Atividades_Tipos::class;
        if (!$tipo = $instanceTipo::where('id', $request->get('atividade')['tipo'])->first()) {
            return back()->withErrors('Tipo de Proposta não encontrado. Por favor, tente novamente.');
        }

        $dados['edicao_atividade_tipo_id'] = $tipo->id;

        /**
         * Valida Tipo de Area de Conhecimento
         */

        $instanceAreaSub = AreaSub::class;
        if (!$areaSub = $instanceAreaSub::where('id', $request->get('atividade')['area_conhecimento'])->first()) {
            return back()->withErrors('Tipo de Área de Conhecimento não encontrado. Por favor, tente novamente.');
        }

        $dados['area_conhecimento_id'] = $areaSub->id;

        /**
         * Valida Titulo
         */
        if (empty($request->get('atividade')['titulo'])) {
            return back()->withErrors('O título da proposta precisa ser preenchido. Por favor, tente novamente.');
        }

        $metadados[] = [
            'chave' => 'titulo',
            'valor' => $request->get('atividade')['titulo']
        ];

        /**
         * Valida Orientador (Se tiver)
         */
        if ($tipo->id == 1) {

            if (isset($request->get('atividade')['orientador'])) {

                if (empty($request->get('atividade')['orientador'])) {

                    return back()->withErrors('O título da proposta precisa ser preenchido. Por favor, tente novamente.');

                }

                $metadados[] = [
                    'chave' => 'orientador',
                    'valor' => $request->get('atividade')['orientador']
                ];

            }

        }

        /**
         * Valida o Resumo
         */
        if (!isset($request->get('atividade')['resumo']) || empty($request->get('atividade')['resumo'])) {
            return back()->withErrors('O título da proposta precisa ser preenchido. Por favor, tente novamente.');
        }

        $metadados[] = [
            'chave' => 'resumo',
            'valor' => htmlentities($request->get('atividade')['resumo'])
        ];

        /**
         * Valida os Participantes da Atividade
         */
        if (!isset($request->get('atividade')['participante']) || empty($request->get('atividade')['participante'])) {
            return back()->withErrors('É necessário pelo menos um participante na atividade. Por favor, tente novamente.');
        }

        $usuarioLogado = auth()->user();
        $arrayParticipantes = $request->get('atividade')['participante'];

        /**
         * Cria Atividade
         */

        $atividade = $usuarioLogado->atividades()->create($dados);
        if (!$atividade) {
            return back()->withErrors('Erro ao criar atividade. Por favor, tente novamente.');
        }

        /**
         * Cria Atividade Participantes
         */
        $instanceUsuario = Usuario::class;
        $participantes = [];

        foreach ($arrayParticipantes as $key => $value) {
            foreach ($value as $kv => $vv) {
                $participantes[$kv][$key] = $vv;
            }
        }

        foreach ($participantes as $key => $value) {
            $usuario = $instanceUsuario::where('id', $value['id'])->first();
            if (!$usuario) {
                unset($participantes[$key]);
                continue;
            }
            $atividade->participantes()->create(
                [
                    'usuario_id' => $usuario->id,
                    'tipo' => $value['tipo'],
                    'instituicao' => $value['instituicao'],
                ]
            );
        }

        /**
         * Cria Metadados
         */
        $atividade->metadados()->createMany($metadados);

        if (!$atividade) {
            return redirect(route('base.atividades'))->withErrors('Erro ao cadastrar atividade. Por favor, tente novamente!');
        }

        if (env('APP_ENV') == 'production') {
            /**
             * Envia email confirmando o cadastro da atividade
             */
            event(new NovaAtividade($atividade));
        }

        return redirect(route('base.atividades'))->withSuccess('Atividade cadastrada com sucesso.');

    }

    public function deleteAtividade($uuid)
    {
        /**
         * Verifica se a atividade existe
         */

        $instanceAtividade = Atividade::where('uuid', $uuid);
        if (!$instanceAtividade->count()) {
            return back()->withErrors('Atividade não encontrdada. Por favor, tente novamente!');
        }

        $atividade = $instanceAtividade->first();
        if ($atividade->status_id == 2) {
            return back()->withErrors('Atividade já excluída.');
        }

        if ($atividade->status_id == 3) {
            return back()->withErrors('Atividade já aprovada. Não é possível a sua exclusão.');
        }

        $atividade->update(
            [
                'status_id' => 2 //Excluído
            ]
        );

        return back()->withSuccess('Atividade excluída com sucesso!');

    }

    public function editAtividade($uuid)
    {
        /**
         * Verifica se a atividade existe
         */

        $instanceAtividade = Atividade::where('uuid', $uuid);
        if (!$instanceAtividade->count()) {
            return back()->withErrors('Atividade não encontrdada. Por favor, tente novamente!');
        }

        $atividade = $instanceAtividade->first();
        if ($atividade->status_id == 2) {
            return back()->withErrors('Atividade já excluída.');
        }

        $edicao = Edicoes::ano();
        if ($edicao->count()) {

            $data['edicao'] = $edicao->first();
            $data['edicao_atividade_tipo_id'] = Edicoes_Atividades_Tipos::where('edicao_id', $edicao->first()->id)
                ->get();
        }


        $data['atividade'] = $atividade;

        $data['proponentes'] = Proponente::where('status_id', 1)
            ->orderBy('descricao')
            ->pluck('descricao', 'id')
            ->toArray();

        //        $data['atividades_tipo'] = Atividade_Tipo::where('status_id', 1)
        //            ->orderBy('descricao')
        //            ->pluck('descricao', 'id')
        //            ->toArray();

        $data['area_sub'] = AreaSub::orderBy('area')->get();

//        if ($atividade->tipos->tipos->id == 1) {
            $data['participacao'] = false;

            if ($atividade->metadados()->where('chave', 'participacao')->count()) {
                $data['participacao'] = true;
            }
//        }

        $data['prazos'] = [
            'inscricoes' => $this->verificaPrazoDeInscricoes(),
            'programacao' => $this->divulgacaoResultado($data['edicao'], 'programacao'),
            'resultado' => $this->divulgacaoResultado($data['edicao'], 'resultado'),
        ];

        if (!$this->verificaPrazoDeInscricoes() || in_array($atividade->status_id, [3, 4])) {
            return view('paginas.atividades.visualizar', $data);
        }

        if($usuarioProponente = $atividade->participantes()->where('tipo', 'proponente')->first()){
            $data['usuarioProponente'] = $usuarioProponente;
        }

        return view('paginas.atividades.editar', $data);
    }

    public function updateAtividade(Request $request, $uuid)
    {
        /**
         * Verifica Prazo de Inscricoes
         */
        if (!$this->verificaPrazoDeInscricoes()) {
            return back()->withErrors('O prazo para inscrições já está encerrado.');
        }

        /**
         * Verifica os UUID's que vem no form e na rota, pra validar
         */
        if (!$request->has('atividade')) {
            return back()->withErrors('Requisição inválida. Tente novamente!');
        }

        if (!$request->has('atividade.uuid') || empty($request->get('atividade')['classe'])) {
            return back()->withErrors('Identificação da atividade vazia. Tente novamente!');
        }

        if ($request->get('atividade')['uuid'] !== $uuid) {
            return back()->withErrors('Identificação da atividade inválida. Tente novamente!');
        }

        /**
         * Verifica se a atividade existe
         */

        $instanceAtividade = Atividade::where('uuid', $uuid);
        if (!$instanceAtividade->count()) {
            return redirect(route('base.atividades'))->withErrors('Atividade não encontrdada. Por favor, tente novamente!');
        }

        $atividade = $instanceAtividade->first();
        if ($atividade->status_id == 2) {
            return redirect(route('base.atividades'))->withErrors('Atividade já excluída.');
        }

        /**
         * Verifica dados do Form
         */

        if (!$request->has('atividade.classe')) {
            return back()->withErrors('Você precisa selecionar a Classe do Proponente. Tente novamente!');
        }

        if ($atividade->proponente_tipo_id != $request->get('atividade')['classe']) {
            $atividade->proponente_tipo_id = $request->get('atividade')['classe'];
        }

        if (!$request->has('atividade.area_conhecimento')) {
            return back()->withErrors('Você precisa selecionar a Área de Conhecimento. Tente novamente!');
        }

        if ($atividade->area_conhecimento_id != $request->get('atividade')['area_conhecimento']) {
            $atividade->area_conhecimento_id = $request->get('atividade')['area_conhecimento'];
        }

        if (!$request->has('atividade.area_conhecimento')) {
            return back()->withErrors('Você precisa selecionar a Área de Conhecimento. Tente novamente!');
        }

        if ($atividade->edicao_atividade_tipo_id != $request->get('atividade')['tipo']) {
            $atividade->edicao_atividade_tipo_id = $request->get('atividade')['tipo'];
        }

        /**
         * Atualiza dados
         */
        $atividade->save();

        /**
         * Metadados
         */

        if ($request->has('atividade.orientador') && !empty($request->get('atividade')['orientador'])) {

            $meta = $atividade->metadados()->where('chave', 'orientador');
            if ($meta->count()) {
                $meta->update(
                    [
                        'valor' => $request->get('atividade')['orientador']
                    ]
                );

            } else {
                $atividade->metadados()->create(
                    [
                        'chave' => 'orientador',
                        'valor' => $request->get('atividade')['orientador']
                    ]
                );
            }
        }

        if ($request->has('atividade.titulo') && !empty($request->get('atividade')['titulo'])) {
            $meta = $atividade->metadados()->where('chave', 'titulo');
            if ($meta->count()) {
                $meta->update(
                    [
                        'valor' => $request->get('atividade')['titulo']
                    ]
                );

            } else {
                $atividade->metadados()->create(
                    [
                        'chave' => 'titulo',
                        'valor' => $request->get('atividade')['titulo']
                    ]
                );
            }
        }

        if ($request->has('atividade.resumo') && !empty($request->get('atividade')['resumo'])) {
            $meta = $atividade->metadados()->where('chave', 'resumo');
            if ($meta->count()) {
                $meta->update(
                    [
                        'valor' => htmlentities($request->get('atividade')['resumo'])
                    ]
                );

            } else {
                $atividade->metadados()->create(
                    [
                        'chave' => 'resumo',
                        'valor' => htmlentities($request->get('atividade')['resumo'])
                    ]
                );
            }
        }

        if ($request->has('atividade.orientador') && !empty($request->get('atividade')['orientador'])) {
            $meta = $atividade->metadados()->where('chave', 'orientador');
            if ($meta->count()) {
                $meta->update(
                    [
                        'valor' => $request->get('atividade')['orientador']
                    ]
                );

            } else {
                $atividade->metadados()->create(
                    [
                        'chave' => 'orientador',
                        'valor' => htmlentities($request->get('atividade')['orientador'])
                    ]
                );
            }
        }

        /**
         * Verifica Participantes
         */

        if ($request->has('atividade.participante')) {
            $arrayParticipantes = $request->get('atividade')['participante'];

            /**
             * Cria Atividade Participantes
             */
            $instanceUsuario = Usuario::class;
            $participantes = [];

            foreach ($arrayParticipantes as $key => $value) {
                foreach ($value as $kv => $vv) {
                    $participantes[$kv][$key] = $vv;
                }
            }

            foreach ($participantes as $key => $value) {
                $usuario = $instanceUsuario::where('id', $value['id'])->first();
                if (!$usuario) {
                    unset($participantes[$key]);
                    continue;
                }

                if ($atividade
                    ->participantes()
                    ->where('usuario_id', $usuario->id)
                    ->where('tipo', $value['tipo'])
                    ->count()) {
                    unset($participantes[$key]);
                    continue;
                }

                $atividade->participantes()->create(
                    [
                        'usuario_id' => $usuario->id,
                        'tipo' => $value['tipo'],
                        'instituicao' => $value['instituicao'],
                    ]
                );
            }
        } else {
            if (!$atividade->participantes()->count()) {
                return back()->withWarning('A atividade precisa de pelo menos um participante. Por favor, verifique!');
            }
        }

        return redirect(route('base.atividades'))->withSuccess('Atividade atualizada com sucesso!');

    }

    public function deleteUsuarioAtividade($id)
    {

        try {
            $atividadeUsuarioId = decrypt($id);

        } catch (DecryptException $e) {
            return back()->withErrors('Participante da atividade não encontrado. Tente novamente!');
        }

        $instanceAtividadeUsuario = Atividade_Usuarios::find($atividadeUsuarioId);
        if (!$instanceAtividadeUsuario) {
            return back()->withErrors('Participante não encontrado na atividade. Tente novamente!');
        }

        if (!$instanceAtividadeUsuario->delete()) {
            return back()->withErrors('Erro ao excluir participante da atividade. Tente novamente!');
        }

        return back()->withSuccess('Participante excluído da atividade com sucesso!');
    }

    public function verificaPrazoDeInscricoes()
    {
        /**
         * Verifica o prazo de inscrições da edição atual
         */

        $instanceEdicao = Edicoes::ano();
        $edicao = $instanceEdicao->first();

        $now = Carbon::now();
        $inscricaoInicio = Carbon::parse($edicao->data_inicio_inscricoes);
        $inscricaoFim = Carbon::parse($edicao->data_fim_inscricoes);

        if (!$now->between($inscricaoInicio, $inscricaoFim)) {
            return false;
        }

        return true;
    }

    public function uploadAtividade(Request $request)
    {
        /**
         * Verifica se Atividade existe
         */

        $uuid = $request->get('atividade')['id'];

        $atividade = Atividade::where('uuid', $uuid);
        if (!$atividade->count()) {
            return response()->json('Atividade não encontrada.', 400);
        }

        if (in_array($atividade->first()->status_id, [2])) {
            return response()->json('Atividade excluída não pode receber arquivos.', 400);
        }

        $atividade = $atividade->first();

        $edicao = Edicoes::ano()->first();

        if($edicao->id != $atividade->edicao()->id){
            return response()->json('Esta atividade está encerrada e não pode receber arquivos.', 400);
        }

        $path = storage_path() . '/atividades/' . $atividade->id;

        $storage = new StorageController();
        $upload = $storage->upload($request, $path)->getContent();

        $upload = json_decode($upload)->files;

        $insertArquivo = $atividade->arquivos()->create(
            [
                'descricao' => $upload->descricao,
                'nome' => $upload->nome,
                'tamanho' => $upload->tamanho,
                'path' => $upload->path,
                'extensao' => $upload->extensao,
            ]
        );

        return response()->json(
            [
                'mensagem' => 'Arquivo enviado com sucesso!',
                'link' => route('base.storage.delete', $insertArquivo->uuid),
                'uuid' => $insertArquivo->uuid,
            ], 200);
    }

    public function informarParticipacao($uuid, $valor)
    {
        /**
         * Verifica se a atividade existe
         */

        $instanceAtividade = Atividade::where('uuid', $uuid);
        if (!$instanceAtividade->count()) {
            return back()->withErrors('Atividade não encontrdada. Por favor, tente novamente!');
        }

        $atividade = $instanceAtividade->first();
        if ($atividade->status_id != 3) {
            return back()->withErrors('Atividade não aprovada.');
        }

        if (!in_array($valor, ['sim', 'nao'])) {
            return back()->withErrors('Erro ao obter resultado da sua participação. Por favor, tente novamente!');
        }

        if ($valor == 'sim') {
            $mensagem = 'O usuário ' . \auth()->user()->nome . ' CONFIRMOU a participação na atividade ' . $atividade->id . ' em ' . Carbon::now()->format('d/m/Y H:i:s');
        }

        if ($valor == 'nao') {
            $mensagem = 'O usuário ' . \auth()->user()->nome . ' RECUSOU a participação na atividade ' . $atividade->id . ' em ' . Carbon::now()->format('d/m/Y H:i:s');
        }

        /**
         * Verifica se existe metadados de confirmação de participação
         */

        if ($count = $atividade->metadados()->where('chave', 'participacao')->count()) {

            if ($count > 1) {

                $atividade->metadados()->where('chave', 'participacao')->delete();
                $atividade->metadados()->where('chave', 'participacao_mensagem')->delete();

            } else {

                $atividade->metadados()->where('chave', 'participacao')->update(
                    [
                        'valor' => $valor
                    ]
                );

                $atividade->metadados()->where('chave', 'participacao_mensagem')->update(
                    [
                        'valor' => $mensagem
                    ]
                );

            }

        } else {
            $atividade->metadados()->createMany(
                [
                    [
                        'chave' => 'participacao',
                        'valor' => $valor
                    ],
                    [
                        'chave' => 'participacao_mensagem',
                        'valor' => $mensagem
                    ]
                ]
            );
        }

        return back()->withSuccess('Informação de participação inserida com sucesso!');
    }

    public function divulgacaoResultado($edicao, $tipo){

        $campo = "data_divulgacao_" . $tipo;
        $data = $edicao->{$campo};
        if($data->lte(Carbon::now())){
            return true;
        }

        return false;
    }
}
