<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

use App\Models\Metadados;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    static function verificaTransmissao($edicao)
    {
        $transmissoes = $edicao->metadados()->where('chave', 'transmissao')->orderBy('created_at', 'DESC')->first();

        if(!$transmissoes){
            return false;
        }

        $retorno = [];
        
//        $now = Carbon::now()->setTimezone('America/Fortaleza');

//        dd($transmissoes);
        
//        foreach ($transmissoes as $transmissao)
//        {
            $dados = json_decode($transmissoes->valor, true);
            $data = explode('/', $dados['data']);
            $data = $data[2] .'-'. $data[1] .'-'. $data[0];

            $data = Carbon::parse($data . '00:00:00');

            if(!$dados['horario']){

//                if($now->gte($data))
//                {
                    $retorno = $dados;
//                    break;
//                }

            } else {

                $hora = explode(' ',$dados['horario']);

                $data_inicio = $data->format('Y-m-d') . ' ' . $hora[0] . ':00';
                $data_inicio = Carbon::parse($data_inicio);
                $data_fim = $data->format('Y-m-d') . ' ' . $hora[1] . ':00';
                $data_fim = Carbon::parse($data_fim);

//                if($now->gte($data_inicio) && $now->lte($data_fim))
//                {
                    $retorno = $dados;
//                }

            }

//        }
        
        return $retorno;
    }
}
