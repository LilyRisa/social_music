<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;

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
Route::get('category', [CategoryController::class, 'get']);
Route::get('test', function(){
    return \response()->json(['sadsa' => 'ádas']);
});

Route::group(['prefix' => 'post'], function(){
    Route::get('list_post', [PostController::class, 'list_post']);
});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('refresh', [UserApiController::class, 'refresh']);
    Route::get('logout', [UserApiController::class, 'logout']);
    Route::get('get_user', [UserApiController::class, 'get_user']);
    Route::get('list_follow', [UserApiController::class, 'list_follow']);
    Route::get('follow_me', [UserApiController::class, 'follow_me']);
    Route::post('update_profile', [UserApiController::class, 'update_profile']);


    Route::group(['middleware' => 'user.banned'], function(){
        Route::post('upload', [MediaController::class, 'upload']);
        Route::post('upload_img', [MediaController::class, 'upload_img']);
        Route::get('follow/{username}', [UserApiController::class, 'follow']);
        Route::get('unfollow/{username}', [UserApiController::class, 'unfollow']);
    });

    Route::group(['prefix' => 'post'], function(){
        Route::group(['middleware' => 'user.banned'], function(){
            Route::post('upload_post', [PostController::class, 'post']);
            Route::post('update_post/{id}', [PostController::class, 'update']);
        });
    });
    Route::get('products', 'UserApiController@index');
});
