<?php

namespace App\Http\Middleware;

use App\Models\Usuarios\Usuario;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class AuthSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::check()) {
           return redirect('/auth/login')->withErrors('Você precisa fazer o login!');
        }
        /**
         * Recupera os dados da sessao
         * Recupera os dados do usuario logado
         */
        $session = Session::all();
        $usuario = Usuario::find(auth()->user()->id);

        /**
         * Busca os Grupos ativos do Usuário
         */
        $grupo = $usuario->grupos()->where('status_id', 1);

        if (!isset($session['alias'])) {

            /**
             * verifica se o usuario possui possui grupos ativos
             */
            if (!$grupo->count()) {
                return $this->logout('A permissão não está ativa. Por favor, entre em contato com o suporte.');
            }

            /**
             * Verifica se o usuario possui um grupo e se o grupo esta ativo
             */
            if ($grupo->count() == 1) {
                $usuarioGrupo = $grupo->first();

                if (!$usuarioGrupo) {
                    return $this->logout('A permissão não está ativa. Por favor, entre em contato com o suporte.');
                }

                /**
                 * retorna a remissao ativo, adiciona o alias a sessao do usuario
                 */
                $usuarioGrupo->where('status_id', 1)->first();

                if (!$usuarioGrupo) {
                    return $this->logout('O grupo que você tem permissão não está ativo. Por favor, entre em contato com o suporte.');
                }

                $grupoAtivo = $usuarioGrupo->grupo()->first();
                if ($grupoAtivo->status != 1) {
                    return $this->logout('O grupo que você tem permissão não está ativo. Por favor, entre em contato com o suporte.');
                }

                /**
                 * adiciona o alias a sessao e redireciona para o painel
                 */
                session()->push('alias', $grupoAtivo->alias);
                session()->pull('multiplos-perfis');
                /**
                 * verifica se o grupo ativo e usuario em caso de true retorna a rota '/'
                 */

                if ($grupoAtivo->alias == 'usuario') {
                    return redirect('/')->withSuccess('success', 'Seja bem-vindo');
                }

                return redirect()->to(route('base.escolher-edicao'))->send();

            } else {

                session()->pull('multiplos-perfis');
                session()->push('multiplos-perfis', true);
                return redirect()->to(route('auth.escolher-perfil'));

            }
        }

        // prefixo da rota atual
        $aliasAtutal = Request::route()->action['prefix'];
        $aliasAtutal = explode('/', $aliasAtutal);
        if(empty($aliasAtutal[0])) {
            // remove a "/" para comparar com o alias do grupo
            $aliasAtutal = str_replace('/', '', $aliasAtutal[1]);
            $aliasAtutal = explode('/', $aliasAtutal);
        }

        /**
         * verifica se a sessao ja possui um alias
         * recebe o alias da sessao atual
         */
        $aliasSession = '';
        if (isset($session['alias'])) {
            $aliasSession  = $session['alias'][0];
        }
        /**
         * verifica se o alias da url atual e o mesmo da sessao do usuario logado
         * caso seja diferente o usuario e redirecionado para o daashboard e alias ativo na sessao
         */
        if ($aliasAtutal[0] != $aliasSession) {
            return redirect()->back()->withErrors('Não e permitido mudar de perfil pela url');
        }

        return $next($request);
    }

    public function logout($mensagem)
    {
        Session::flush();
        return redirect('/')->withErrors($mensagem);
    }
}
