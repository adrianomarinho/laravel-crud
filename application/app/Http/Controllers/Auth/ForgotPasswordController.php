<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmailResetPassword;
use App\Helpers\Validations;
use App\Http\Controllers\Controller;
use App\Models\Usuarios\Usuario;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function validateLogin(Request $request)
    {
        $request->merge(
            [
                'login' => Validations::sanitizeFromInt($request->login)
            ]
        );
        $request->validate(
            [
                'login' => ['required', 'exists:usuarios,login'],
            ],
            [
                'login.required' => 'O documento é obrigatório.',
                'login.exists' => 'O documento não foi encontrado. Por favor, verifique!',
            ]
        );
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateLogin($request);
        $token = Str::random(60);
        $login = preg_replace("/[^0-9_]/", "", $request->login);

        /**
         * Verifica se usuário está cadastrado
         */
        if(!$usuario = Usuario::where('login', $login)->first()){
            return back()->withErrors('Usuário não encontrado. Por favor, tente novamente!');
        }

        $passwordReset = DB::table('password_resets')->insert(
            ['login' => $usuario->login, 'token' => $token, 'created_at' => Carbon::now()]
        );

        if(!$passwordReset){
            return back()->withErrors('Erro ao solicitar token. Por favor, tente novamente!');
        }

        event(new SendEmailResetPassword($usuario, $token));

        return back()->withSuccess('Solicitação enviada com sucesso. Verifique seu email para alterar sua senha!');
    }

}
