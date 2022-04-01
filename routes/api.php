<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\PostController;

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

Route::post('login', [UserApiController::class, 'authenticate']);
Route::post('register', [UserApiController::class, 'register']);
Route::get('test', function(){
    return \response()->json(['sadsa' => 'ádas']);
});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('refresh', [UserApiController::class, 'refresh']);
    Route::get('logout', [UserApiController::class, 'logout']);
    Route::get('get_user', [UserApiController::class, 'get_user']);
    Route::get('follow/{username}', [UserApiController::class, 'follow']);
    Route::get('list_follow', [UserApiController::class, 'list_follow']);
    Route::get('follow_me', [UserApiController::class, 'follow_me']);
    Route::post('upload', [UserApiController::class, 'upload']);

    Route::group(['prefix' => 'post', 'middleware' => 'user.banned'], function(){
        Route::get('list_post', [PostController::class, 'list_post']);
        Route::post('upload_post', [PostController::class, 'post']);
    });
    Route::get('products', 'UserApiController@index');
});
