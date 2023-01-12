<?php

namespace Modules\Base\Http\Controllers;

use App\Models\Usuarios\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public function index()
    {
        return view('base::form');
    }

    public function list(Request $request, $respose = false)
    {
        if($request->has('campo') && $request->has('valor') && !empty($request->get('campo')) && !empty($request->get('valor'))){
            $campo = $request->get('campo');
            $valor = $request->get('valor');

            if($campo == 'nome'){
                $data['usuarios'] = Usuario::where('nome', 'like', '%' . $valor . '%')->get();
            }

            if($campo == 'codigo'){
                $data['usuarios'] = Usuario::where('codigo', 'like', '%' . $valor . '%')->get();
            }

            if($campo == 'cidade'){
                $data['usuarios'] = Usuario::where('cidade', 'like', '%' . $valor . '%')->get();
            }

            if($campo == 'cep'){
                $data['usuarios'] = Usuario::where('cep', 'like', '%' . $valor . '%')->get();
            }

            $data['mensagem'] = 'Registros filtrados por: ' . ucfirst($campo) . ' = <strong>' . strtoupper($valor) . '</strong>';
        } else {
            $data['usuarios'] = Usuario::orderBy('nome', 'desc')->get();
        }

        if($respose){
            return $data;
        }

        return view('base::list', $data);
    }

    public function edit($idUsuario)
    {
        if(!$instanciaUsuario = Usuario::find(decrypt($idUsuario))){
            return back()->withErrors('Usuário não encontrado');
        }

        $data['usuario'] = $instanciaUsuario;

        return view('base::edit', $data);
    }

    public function destroy($idUsuario)
    {
        if(!$instanciaUsuario = Usuario::find(decrypt($idUsuario))){
            return back()->withErrors('Usuário não encontrado');
        }

        if(!$instanciaUsuario->delete()){
            return back()->withErrors('Erro ao remover Usuário. Por favor, tente novamente!');
        }

        return redirect(route('index'))->withSuccess('Usuário removido com sucesso!');
    }

    public function store(Request $request)
    {
        /**
         * Validação de Dados
         */
        $documento = preg_replace('/[^0-9]/', '', $request->get('documento'));
        $request->merge(['documento' => $documento]);
        $request->validate(
            [
                'codigo' => 'required|unique:usuarios,codigo',
                'nome' => 'required',
                'documento' => 'required|unique:usuarios,documento',
                'cep' => 'required',
                'logradouro' => 'required',
                'numero' => 'string|nullable',
                'bairro' => 'required',
                'cidade' => 'required',
                'uf' => 'required',
                'complemento' => 'string|nullable',
                'fone' => 'required',
                'limite' => 'required',
                'validade' => 'required',
            ],
            [
                'required' => 'O campo :attribute é de preenchimento obrigatório.',
                'unique' => 'O Documento já existe.',
            ]
        );

        $limite = preg_replace('/[^0-9]/', '', $request->get('limite'));

        /**
         * Cria instancia do Usuário
         */
        $instanciaUsuario = Usuario::create(
            [
                'codigo' => $request->get('codigo'),
                'nome' => $request->get('nome'),
                'documento' => $request->get('documento'),
                'cep' => $request->get('cep'),
                'logradouro' => $request->get('logradouro'),
                'numero' => $request->get('numero'),
                'bairro' => $request->get('bairro'),
                'cidade' => $request->get('cidade'),
                'uf' => $request->get('uf'),
                'complemento' => $request->get('complemento'),
                'fone' => $request->get('fone'),
                'limiteCredito' => $limite,
                'validade' => $request->get('validade'),
                'dataHoraCadastro' => Carbon::now(),
            ]
        );

        if(!$instanciaUsuario){
            return back()->withErrors('Erro ao inserir usuário.');
        }

        return back()->withSuccess('Usuário inserido com sucesso!');
    }

    public function update(Request $request)
    {
        /**
         * Validação de Dados
         */
        $documento = preg_replace('/[^0-9]/', '', $request->get('documento'));
        $request->merge(['documento' => $documento]);
        $request->validate(
            [
                'id' => 'required',
                'codigo' => 'required',
                'nome' => 'required',
                'documento' => 'required',
                'cep' => 'required',
                'logradouro' => 'required',
                'numero' => 'string|nullable',
                'bairro' => 'required',
                'cidade' => 'required',
                'uf' => 'required',
                'complemento' => 'string|nullable',
                'fone' => 'required',
                'limite' => 'required',
                'validade' => 'required',
            ],
            [
                'required' => 'O campo :attribute é de preenchimento obrigatório.',
            ]
        );

        /**
         * Cria instancia do Usuário
         */
        $instanciaUsuario = Usuario::find(decrypt($request->get('id')));

        if(!$instanciaUsuario){
            return back()->withErrors('Erro ao atualizar usuário.');
        }

        $instanciaUsuario->update(
            [
                'codigo' => $request->get('codigo'),
                'nome' => $request->get('nome'),
                'documento' => $request->get('documento'),
                'cep' => $request->get('cep'),
                'logradouro' => $request->get('logradouro'),
                'numero' => $request->get('numero'),
                'bairro' => $request->get('bairro'),
                'cidade' => $request->get('cidade'),
                'uf' => $request->get('uf'),
                'complemento' => $request->get('complemento'),
                'fone' => $request->get('fone'),
                'limite' => $request->get('limite'),
                'validade' => $request->get('validade'),
            ]
        );

        return redirect(route('list'))->withSuccess('Usuário atualizado com sucesso!');
    }
}
