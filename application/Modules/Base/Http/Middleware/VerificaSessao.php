<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificaSessao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()){
            Auth::logout();
            return redirect(route('login'))->withErrors('Sessão expirada. Por favor, faça o login novamente.');
        }

        $usuario = auth()->user();

        /**
         * Busca Grupos do Usuário
         */

        $grupos = $usuario->grupos()->where('status', 1);

        if(!$countGrupos = $grupos->count()){
            $this->logout('Você não tem nenhum perfil ativo. Por favor, entre em contato com o suporte da SECT.');
        }

        if($countGrupos == 1){
            $usuarioGrupo = $grupos->first();
            if(!$usuarioGrupo){
                $this->logout('A permissão não está ativa. Por favor, entre em contato com o suporte da SECT.');
            }

            $grupo = $usuarioGrupo->grupo()->where('status', 1)->first();
            if(!$grupo){
                $this->logout('O grupo que você tem permissão não está ativo. Por favor, entre em contato com o suporte da SECT.');
            }

            if(session()->exists('alias')){
                session()->forget('alias');
            }

            session()->push('alias', $grupo->alias);
            return redirect()->to(config('snct.url_base'))->send();

        } else {
            return redirect()->to(route('base.escolher-perfil'))->send();
        }

//        return $next($request);
    }

    public function logout($mensagem, $status = 'Errors'){
        Auth::logout();
        return redirect('/')->withErrors('Faça o login.');
    }

}
