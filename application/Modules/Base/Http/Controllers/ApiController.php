<?php

namespace Modules\Base\Http\Controllers;

use App\Models\Usuarios\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;

class ApiController extends Controller
{

    public function list(Request $request)
    {
        if ($request->has('campo') && $request->has('valor') && !empty($request->get('campo')) && !empty($request->get('valor'))) {

            $campo = $request->get('campo');
            $valor = $request->get('valor');

            if ($campo == 'nome') {
                $data['usuarios'] = Usuario::where('nome', 'like', '%' . $valor . '%')->get();
            }

            if ($campo == 'codigo') {
                $data['usuarios'] = Usuario::where('codigo', 'like', '%' . $valor . '%')->get();
            }

            if ($campo == 'cidade') {
                $data['usuarios'] = Usuario::where('cidade', 'like', '%' . $valor . '%')->get();
            }

            if ($campo == 'cep') {
                $data['usuarios'] = Usuario::where('cep', 'like', '%' . $valor . '%')->get();
            }

            if (!count($data['usuarios'])) {
                $data['mensagem'] = 'Nenhum registro encontrado.';
                return response()->json($data, 400);
            } else {
                $data['mensagem'] = 'Registros filtrados por: ' . ucfirst($campo) . ' = <strong>' . strtoupper($valor) . '</strong>';
            }
            return response()->json($data, Response::HTTP_OK);
        }

        return response()->json(
            [
                'mensagem' => 'Busca sem parâmetros.',
                'data' => [],
            ], Response::HTTP_BAD_REQUEST);

    }

    public function destroy(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
            ],
            [
                'required' => 'O campo :attribute é de preenchimento obrigatório.',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try {

            /**
             * Cria instancia do Usuário
             */
            $instanciaUsuario = Usuario::find($request->get('id'));

            if (!$instanciaUsuario) {
                return response()->json(['mensagem' => 'Usuário não encontrado.'], Response::HTTP_BAD_REQUEST);
            }

            $instanciaUsuario->delete();
            return response()->json(['mensagem' => 'Usuário excluido com sucesso.'], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json(
                [
                    'mensagem' => 'Erro ao atualizar usuário.',
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);

        }
    }

    public function store(Request $request)
    {
        $documento = preg_replace('/[^0-9]/', '', $request->get('documento'));
        $request->merge(['documento' => $documento]);

        $validator = Validator::make(
            $request->all(),
            [
                'codigo' => 'required|unique:usuarios,codigo',
                'nome' => 'required',
                'documento' => 'required|unique:usuarios,documento',
                'cep' => 'required',
                'logradouro' => 'required',
                'numero' => 'string',
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try {

            /**
             * Cria instancia do Usuário
             */
            $limite = preg_replace('/[^0-9]/', '', $request->get('limite'));
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

            return response()->json(['mensagem' => 'Usuário inserido com sucesso!'], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json(
                [
                    'mensagem' => 'Erro ao inserir usuário.',
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
        }

    }

    public function update(Request $request)
    {
        $documento = preg_replace('/[^0-9]/', '', $request->get('documento'));
        $request->merge(['documento' => $documento]);

        $validator = Validator::make(
            $request->all(),
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
                'unique' => 'O :attribute já existe.',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try {

            /**
             * Cria instancia do Usuário
             */
            $instanciaUsuario = Usuario::find($request->get('id'));

            if (!$instanciaUsuario) {
                return response()->json(['mensagem' => 'Usuário não encontrado.'], Response::HTTP_BAD_REQUEST);
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

            return response()->json(['mensagem' => 'Usuário atualizado com sucesso!'], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json(
                [
                    'mensagem' => 'Erro ao atualizar usuário.',
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);

        }
    }
}
