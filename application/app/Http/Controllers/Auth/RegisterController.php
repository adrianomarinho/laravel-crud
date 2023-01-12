<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmailRegister;
use App\Helpers\Validations;
use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Usuarios\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
//    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['usuario']['login'] = preg_replace("/[^0-9]/", "", $data['usuario']['login']);

        Validator::make($data['usuario'], [
            'login' => ['required', 'unique:usuarios', 'min:11', 'max:14'],
            'nome' => ['required', 'min:5',],
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'password' => ['required', 'same:confirm_password', 'min:6'],
            'confirm_password' => ['required'],
        ], [
            'login.required' => 'O documento é obrigatório.',
            'login.unique' => 'O documento informado já esta cadastrado no sistema. Faça o login.',
            'login.min' => 'O documento informado está fora do tamanho esperado.',
            'login.max' => 'O documento informado está fora do tamanho esperado.',
            'nome.required' => 'O nome precisa ser preenchido.',
            'nome.min' => 'O nome precisa ter pelo menos 5 letras.',
            'password.required' => 'O campo senha é obrigatório.',
            'confirm_password.required' => 'O campo confirmação de senha é obrigatório.',
            'password.same' => 'O campo senha deve ser igual ao campo de confirmação de senha .',
        ])->validate();

        /**
         * Valida nome e dados do usuário
         */

        $dados = Validator::make($data, [
//            'aceite' => ['required', 'boolean'],
//            'email' => ['required', 'min:1', 'email:rfc,dns'],
//            'telefone' => ['required'],
//            'sexo' => ['required', 'min:1', 'max:1'],
//            'dados.data_nascimento' => ['required', 'date'],
//            'dados.escolaridade' => ['required'],
//            'password' => ['required', 'same:password.confirm_password', 'min:6'],
//            'confirm_password' => ['required'],
        ], [
            'aceite.required' => 'Você precisa aceitar os termos do edital para finalizar seu cadastro.',
            'aceite.boolean' => 'Você precisa aceitar os termos do edital para finalizar seu cadastro.',
            'email.required' => 'Você precisa digitar um email válido.',
//            'telefone.required' => 'Você precisa digitar um telefone válido.',
//            'sexo.required' => 'Selecione o sexo do usuário.',
//            'sexo.min' => 'Selecione o sexo do usuário.',
//            'sexo.max' => 'Selecione o sexo do usuário.',
//            'dados.data_nascimento.required' => 'Você precisa digitar uma data de nascimento válida.',
//            'dados.escolaridade.required' => 'Selecione sua escolaridade.',
//            'password.required' => 'O campo senha é obrigatório.',
//            'confirm_password.required' => 'O campo confirmação de senha é obrigatório.',
//            'password.same' => 'O campo senha deve ser igual ao campo de confirmação de senha .',
        ]);

        return $dados;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $login = preg_replace("/[^0-9_]/", "", $data['usuario']['login']);
        $usuario = Usuario::create([
            'nome' => $data['usuario']['nome'],
            'login' => $login,
            'tipo' => 'PF',
            'status_id' => 0, //INATIVO
            'senha' => Hash::make($data['usuario']['password']),
        ]);

        if(!$usuario){
            return back()->withErrors('Erro ao criar usuário. Por favor, tente novamente!');
        }

        /**
         * Criar Grupo de Usuário
         */

        $usuario->grupos()->create(
            [
                'grupo_id' => 4, //Usuario
                'status' => 1, //Usuario
            ]
        );

        /**
         * Salvando documentos do usuario
         */

        $documentos = [
            [
                'chave' => 'CPF',
                'valor' => Validations::formatCpfCnpj($data['usuario']['login']),
                'status_id' => Status::ATIVO,
            ]
        ];
        $usuario->documentos()->createMany($documentos);

        /**
         * Salvando documentos do usuario
         */

        $telefones = [
            [
                'chave' => 'telefone',
                'valor' => $data['telefone'],
            ]
        ];

        $usuario->telefones()->createMany($telefones);

        /**
         * salvando dados do usuario
         */
//        $dados = [];
//        foreach ($data['dados'] as $key => $value) {
//            $dados[] = [
//                'chave' => $key,
//                'valor' => $value,
//            ];
//        }
//        $usuario->dados()->createMany($dados);

        /**
         * salvando emails
         */
        $usuario->emails()->create([
            'chave' => 'email',
            'valor' => $data['usuario']['email'],
            'status_id' => Status::ATIVO,
        ]);

        /**
         * Envia email para ativação do cadastro
         */

        event(new SendEmailRegister($usuario));

        return $usuario;
    }
}
