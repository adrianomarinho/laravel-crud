<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmailRegister;
use App\Http\Controllers\Controller;
use App\Models\Usuarios\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Helpers\Validations;
use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\LogsController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected function redirectTo()
    {
        return route('auth.escolher-perfil');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * redireciona para a rota '/' quando acessado a rota "/login" METODO GET
     */
    public function showLoginForm()
    {
        return redirect('/');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'senha' => 'required'
        ],
            [
                'senha.required' => 'A senha é de preenchimento obrigatório.'
            ]);

        $request->validate([
            'login' => 'required'
        ],
            [
                'login.required' => 'O Login é de preenchimento obrigatório.'
            ]);

        $login = preg_replace("/[^0-9_]/", "", $request->login);

        if(!$tamanho = strlen($login)){
            $request->validate([
                'login' => 'boolean',
            ],
                [
                    'login.boolean' => 'O login ' . $request->login . '  é invalido. Por favor, verifique!'
                ]);
        }

        if($tamanho > 11 && $tamanho <= 14){
            if (!Validations::cnpj($login)) {
                $request->validate([
                    'login' => 'boolean',
                ],
                    [
                        'login.boolean' => 'O login ' . $request->login . '  não é valido. Por favor, verifique!'
                    ]);
            }
        } else {
            if (!Validations::cpf($login)) {
                $request->validate([
                    'login' => 'boolean',
                ],
                    [
                        'login.boolean' => 'O login ' . $request->login . '  não é valido. Por favor, verifique!'
                    ]);
            }
        }

        /**
         * Salva log de Acesso ao sistema
         */
        $log = new LogsController();
        $log =  $log->add('auth', null, null, 'Login', 'O usuário ' . $request->login . ' solicitou acesso ao sistema.', $request->login, null, null);

        /**
         * Verifica se o usuário existe
         */
        $instanceUsuario = Usuario::class;
        if (!$instanceUsuario::where('login', $login)->count()) {
            return false;
        }

        $statusUsuario = $instanceUsuario::where('login', $login)->first();
        if (!$statusUsuario->status_id) {
            event(new SendEmailRegister($statusUsuario));
            $request->validate([
                'login' => 'boolean',
            ], [
                'login.boolean' => 'Usuário com acesso inativo. Por favor, verifique seu e-mail ou entre em contato com o suporte.',
            ]);
        }

        if ($statusUsuario->status_id > 1) {
            $request->validate([
                'login' => 'boolean',
            ], [
                'login.boolean' => 'Usuário com acesso bloqueado. Por favor, entre em contato com o suporte.',
            ]);
        }

        /**
         * Salva Logs de Solicitação de Usuários
         */

        $array = [
            'action' => 'Solicitou acesso ao sistema',
            'login' => $request->login
        ];
    }

    /**
     * muda o padrao de password para senha
     */
    protected function credentials(Request $request)
    {
        /**
         * remove todos os caracteres digitados
         * e adiciona ao request o cpf com apenas digitos
         */
        $login = preg_replace("/[^0-9_]/", "", $request->login);
        $request->merge(['login' => $login]);

        return $request->only($this->username(), 'senha');
    }

    /**
     * muda o padrao de email para login
     */
    public function username()
    {
        return 'login';
    }
}
