<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so we do not have to manually load any of
| our application's PHP classes. It just feels great to relax.
|
*/

$erro_message = 'Desculpe, estamos atualizando o <strong>CARTÓRIOS MARANHÃO</strong>. Atualize a página ou tente novamente em alguns instantes!';

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    include(__DIR__ . '/../public/atualizacao.blade.php');
    die();
}

require_once __DIR__.'/../vendor/autoload.php';

