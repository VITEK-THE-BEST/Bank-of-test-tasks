<?php

use App\Http\Controllers\UserController;
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

Route::middleware(['api', 'auth:sanctum'])->group(function () {

    Route::group(['prefix' => 'user'], function () {
        Route::controller(UserController::class)->group( function () {

            Route::get('/me', 'me');
            Route::delete('/dropToken', 'dropToken');
            Route::patch('/update', 'update');
            Route::delete('/delete/{id}', 'delete');
        });
    });






});
