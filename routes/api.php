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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('testsend', 'App\Http\Controllers\TestApiController@TestSend');
Route::post('getapidomain', 'App\Http\Controllers\ToolsController@getapidomain');
Route::post('uploadimage', 'App\Http\Controllers\ToolsController@uploadimage');
Route::get('killdrop/{drop_id}', 'App\Http\Controllers\TestApiController@kill_drop');
Route::get('killwarmup/{warmup_id}', 'App\Http\Controllers\TestApiController@kill_warmup');

Route::post('startSend', 'App\Http\Controllers\TestApiController@StartSend');

Route::get('getAccountantFile/{name}', 'App\Http\Controllers\accountantFilesController@getFile');

Route::get('getDkim_key', 'App\Http\Controllers\DkimController@getKey');
