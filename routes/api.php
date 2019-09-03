<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'cors'],function(){
    Route::get('test','SyncController@test');
    Route::get('pull/{id}','SyncController@pull');
    Route::post('push/{id}','SyncController@push');
    Route::get('stats/{id}','SyncController@stats');
    Route::get('review/{id}','SyncController@review');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
