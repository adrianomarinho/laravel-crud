<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuarios\Usuario;
use Illuminate\Http\Request;

class AtivaCadastro extends Controller
{
    public function index($uuid){
        if(!$uuid){
            return redirect('/')->withErrors('Usuário Inválido. Por favor, tente novamente.');
        }

        $instanceUsuario = Usuario::class;

        if(!$usuario = $instanceUsuario::where('uuid', $uuid)->first()){
            return redirect('/')->withErrors('Usuário não encontrado. Por favor, tente novamente.');
        }

        if(!$usuario->status){

            $usuario->update(
                [
                    'status_id' => $usuario::ATIVO
                ]
            );

            return redirect('/')->withSuccess('Usuário ativado com sucesso. Por favor, acesse com seu Login e Senha.');

        } else {
            /**
             * Verifica se o usuário está cancelado
             */
            if($usuario->status == $instanceUsuario::CANCELADO){
                return redirect('/')->withErrors('Usuário com acesso bloqueado. Por favor, entre em contato com o suporte da SECTI.');
            }

            /**
             * Verifica se o usuário está ativo
             */
            if($usuario->status == $instanceUsuario::ATIVO){
                return redirect('/')->withErrors('Este Usuário já está ativo. Por favor, acesse com seu Login e Senha.');
            }
        }
    }
}
