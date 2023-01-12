<?php

namespace App\Models\Usuarios;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'idUsuario',
        'dataHoraCadastro',
        'codigo',
        'nome',
        'documento',
        'cep',
        'logradouro',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'complemento',
        'fone',
        'limiteCredito',
        'validade',
    ];

    protected $dates = [
        'dataHoraCadastro',
        'validade',
    ];

}
