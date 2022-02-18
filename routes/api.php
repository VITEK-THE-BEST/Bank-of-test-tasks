<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\UserController;
use App\Models\Discipline;
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

/**
 * @unauthenticated
*/
Route::post('/registration', [UserController::class, 'register']);
/**
* @unauthenticated
*/
Route::post('/getToken', [UserController::class, 'getToken']);


Route::middleware(['api', 'auth:sanctum'])->group(function () {

    Route::group(['prefix' => 'user'], function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/me', 'me');
            Route::delete('/dropToken', 'dropToken');
            Route::patch('/update', 'update');
            Route::delete('/delete/{id}', 'delete');
        });
    });

    Route::group(['prefix' => 'discipline'], function () {
        Route::controller(UserController::class)->group(function () {
            Route::post('/create', 'create');
            Route::post('/addUsers/{id}', 'addUsers');
            Route::post('/addBank/{id}/{id}', 'addBank');
            Route::delete('/delete/{id}', 'delete');
            Route::put('/update/{id}', 'update');
        });
    });

    Route::group(['prefix' => 'discipline'], function () {
        Route::controller(UserController::class)->group(function () {
            Route::post('/create', 'create');
            Route::post('/addUsers/{id}', 'addUsers');
            Route::post('/addBank/{id}/{id}', 'addBank');
            Route::delete('/delete/{id}', 'delete');
            Route::put('/update/{id}', 'update');
            Route::get('/show', 'show');
        });
    });

    Route::group(['prefix' => 'bank'], function () {
        Route::controller(BankController::class)->group(function () {
            Route::post('/create', 'create');
            Route::get('/show', 'show');
            Route::put('/update/{id}', 'update');
            Route::delete('/delete/{id}', 'delete');
        });
    });
});
