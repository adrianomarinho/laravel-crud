<?php

Route::group([
    'middleware' => ['web'],
    'namespace' => '\Modules\Base\Http\Controllers'
], function(){

    Route::get('/', 'BaseController@index')->name('index');
    Route::post('/', 'BaseController@store')->name('store');

    Route::get('/list', 'BaseController@list')->name('list');

    Route::get('/edit/{idUsuario}', 'BaseController@edit')->name('edit');
    Route::post('/update', 'BaseController@update')->name('update');

    Route::get('/destroy/{idUsuario}', 'BaseController@destroy')->name('destroy');
});

