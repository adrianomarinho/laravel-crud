<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmailRegister;
use App\Helpers\Validations;
use App\Http\Controllers\Controller;
use App\Models\Usuarios\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function reset(Request $request)
    {
        $request->merge(['login' => Validations::sanitizeFromInt($request->login)]);
        $request->validate(
            [
                'login' => ['required', 'exists:usuarios,login'],
                'password' => ['required', 'same:password_confirmation', 'min:8'],
                'password_confirmation' => ['required'],
            ],
            [
                'login.required' => 'O documento é obrigatório.',
                'login.exists' => 'O documento não foi encontrado. Por favor, verifique!',
                'password.required' => 'O campo senha é obrigatório.',
                'password.min' => 'O campo senha precisa ter no mínimo 8 caracteres.',
                'password_confirmation.required' => 'O campo confirmação de senha é obrigatório.',
                'password.same' => 'O campo senha deve ser igual ao campo de confirmação de senha .',
            ]
        );

        /**
         * Verifica se Token existe na tabela de password_resets
         */
        $instancePasswordResets = DB::table('password_resets')->where('token', $request->token);

        if (!$instancePasswordResets->count()) {
            return redirect('/')->withErrors('Token de Reset de Senha não encontrado. Por favor, tente novamente!');
        }

        if ($instancePasswordResets->count() > 1) {
            $instancePasswordResets->delete();
            return redirect('/')->withErrors('Token de Reset de Senha inválido. Por favor, solicite novamente!');
        }

        $passwordResets = $instancePasswordResets->first();

        if (!$passwordResets->login) {
            $passwordResets->delete();
            return redirect('/')->withErrors('Token de Reset de Senha inválido. Por favor, solicite novamente!');
        }

        /**
         * Verifica se Usuário existe
         */

        $instanceUsuario = Usuario::where('login', $passwordResets->login);

        if (!$instanceUsuario->count()) {
            $instancePasswordResets->delete();
            return redirect('/')->withErrors('Usuário não encontrado. Por favor, solicite novamente!');
        }

        $usuario = $instanceUsuario->first();

        if (!$usuario->status) {
            $instancePasswordResets->delete();
            event(new SendEmailRegister($usuario));
            return redirect('/')->withErrors('Usuário inativo. Por favor, ative seu cadastro através do email que enviamos no ato do cadastro!');
        }

        if ($usuario->status == 2) {
            $instancePasswordResets->delete();
            return redirect('/')->withErrors('Usuário sem acesso ao painel. Por favor, entre em contato com o suporte da SECTI!');
        }

        $usuario->update(
            [
                'senha' => Hash::make($request->password)
            ]
        );

        $instancePasswordResets->delete();

        return redirect('/')->withSuccess('Dados alterados com sucesso!');

    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'login', 'password', 'password_confirmation', 'token'
        );
    }
}
