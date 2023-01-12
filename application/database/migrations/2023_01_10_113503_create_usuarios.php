<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->integer('idUsuario');
            $table->dateTime('dataHoraCadastro');
            $table->integer('codigo');
            $table->string('nome');
            $table->string('documento');
            $table->string('cep');
            $table->string('logradouro');
            $table->string('endereco');
            $table->integer('numero')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('uf');
            $table->string('complemento')->nullable();
            $table->string('fone');
            $table->float('limiteCredito', 6, 2);
            $table->date('validade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
