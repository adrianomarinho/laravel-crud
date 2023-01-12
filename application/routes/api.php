<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => ['api'],
    'namespace' => '\Modules\Base\Http\Controllers'
], function(){

    Route::get('/search', 'ApiController@list')->name('api.list');

    Route::post('/store', 'ApiController@store')->name('api.store');

    Route::put('/update', 'ApiController@update')->name('api.update');

    Route::delete('/delete', 'ApiController@destroy')->name('api.destroy');
});
