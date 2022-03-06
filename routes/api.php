<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\SectionController;
use \App\Http\Controllers\QuestionController;
use \App\Http\Controllers\CategoryController;
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
        Route::get('/me', [UserController::class, 'me']);
        Route::delete('/dropToken', [UserController::class, 'dropToken']);
        Route::patch('/update', [UserController::class, 'update']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
    });

    Route::group(['prefix' => 'discipline'], function () {
        Route::post('/create', [DisciplineController::class, 'create']);
        Route::post('/addUsers/{id}', [DisciplineController::class, 'addUsers']);
        Route::post('/addBank/{id}/{id}', [DisciplineController::class, 'addBank']);
        Route::delete('/delete/{id}', [DisciplineController::class, 'delete']);
        Route::put('/update/{id}', [DisciplineController::class, 'update']);
    });

    Route::group(['prefix' => 'bank'], function () {
        Route::post('/create', [BankController::class, 'create']);
        Route::get('/show', [BankController::class, 'show']);
        Route::put('/update/{id}', [BankController::class, 'update']);
        Route::delete('/delete/{id}', [BankController::class, 'delete']);
    });

    Route::group(['prefix' => 'section'], function () {
        Route::post('/create/{id}', [SectionController::class, 'create']);
        Route::get('/show/{id}', [SectionController::class, 'show']);
        Route::put('/update/{id}', [SectionController::class, 'update']);
        Route::delete('/delete/{id}', [SectionController::class, 'delete']);
        Route::post('/createCategory/{id}/{id}', [SectionController::class, 'createCategory']);
        Route::post('/deleteCategory/{id}/{id}', [SectionController::class, 'deleteCategory']);
        Route::post('/showNotCategory', [SectionController::class, 'showNotCategory']);
        Route::post('/showCategory/{id}', [SectionController::class, 'showNotCategory']);
    });
    Route::group(['prefix' => 'category'], function () {
        Route::post('/create', [CategoryController::class, 'create']);
        Route::get('/show', [CategoryController::class, 'show']);
        Route::put('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
    });

    Route::group(['prefix' => 'question'], function () {
        Route::post('/create/{id}', [QuestionController::class, 'create']);
        Route::put('/update/{id}', [QuestionController::class, 'update']);
        Route::delete('/delete/{id}', [QuestionController::class, 'delete']);
        Route::get('/show/{id}', [QuestionController::class, 'show']);
        Route::get('/count/{id}', [QuestionController::class, 'count']);
        Route::get('/take/{id}', [QuestionController::class, 'take']);
    });
});
