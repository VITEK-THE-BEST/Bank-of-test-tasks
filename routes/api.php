<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\FileLoadController;
use App\Http\Controllers\PassTestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTestController;
use \App\Http\Controllers\SectionController;
use \App\Http\Controllers\QuestionController;
use \App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;




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

//        Route::group(['prefix' => 'test'], function () {
//            Route::post('/create/{bank}', [UserTestController::class, 'create']);
//            Route::get('/show', [UserTestController::class, 'show']);
//            Route::patch('/update/{userTest}', [UserTestController::class, 'update']);
//            Route::delete('/delete/{userTest}', [UserTestController::class, 'delete']);
//            Route::get('/completed/{userTest}', [UserTestController::class, 'completed']);
//        });
    });


//    Route::group(['prefix' => 'discipline'], function () {
//        Route::post('/create', [DisciplineController::class, 'create']);
//        Route::post('/addUsers/{id}', [DisciplineController::class, 'addUsers']);
//        Route::get('/addBank/{discipline}/bank/{bank}', [DisciplineController::class, 'addBank']);
//        Route::delete('/delete/{id}', [DisciplineController::class, 'delete']);
//        Route::put('/update/{id}', [DisciplineController::class, 'update']);
//    });

    Route::group(['prefix' => 'bank'], function () {
        Route::post('/create', [BankController::class, 'create']);
        Route::get('/show', [BankController::class, 'show']);
        Route::get('/showDetails/{bank}', [BankController::class, 'showDetails']);
        Route::put('/update/{id}', [BankController::class, 'update']);
        Route::delete('/delete/{id}', [BankController::class, 'delete']);
        Route::get('/showUnload', [BankController::class, 'showUnload']);
    });

    Route::group(['prefix' => 'section'], function () {
        Route::post('/create/{bank}', [SectionController::class, 'create']);
        Route::get('/show/{bank}', [SectionController::class, 'show']);
        Route::put('/update/{section}', [SectionController::class, 'update']);
        Route::delete('/delete/{section}', [SectionController::class, 'delete']);
        Route::get('/createCategory/{section}/category/{category}', [SectionController::class, 'createCategory']);
        Route::delete('/deleteCategory/{section}/category/{category}', [SectionController::class, 'deleteCategory']);
        Route::get('/showNotCategory/{section}', [SectionController::class, 'showNotCategory']);
        Route::get('/showCategory/{section}', [SectionController::class, 'showCategory']);
    });

    Route::group(['prefix' => 'category'], function () {
        Route::post('/create', [CategoryController::class, 'create']);
        Route::put('/update/{category}', [CategoryController::class, 'update']);
        Route::delete('/delete/{category}', [CategoryController::class, 'delete']);
        Route::get('/show', [CategoryController::class, 'show']);
        Route::get('/showDetail/{category}', [CategoryController::class, 'showDetail']);
    });

    Route::group(['prefix' => 'question'], function () {
        Route::post('/create/category/{category}', [QuestionController::class, 'create']);
        Route::put('/update/{question}', [QuestionController::class, 'update']);
        Route::delete('/delete/{question}', [QuestionController::class, 'delete']);
        Route::get('/show/{category}', [QuestionController::class, 'show']);
        Route::get('/count/{category}', [QuestionController::class, 'count']);
        Route::get('/take/{question}', [QuestionController::class, 'take']);
    });

    Route::group(['prefix' => 'files'], function () {
        Route::get('/unloadingBank/bank/{bank}', [FileLoadController::class, 'unloadingBank']);
        Route::post('/loadingBank', [FileLoadController::class, 'loadingBank']);
        Route::post('/passport/{bank}', [FileLoadController::class, 'passport']);
    });

});

//Route::group(['prefix' => 'test'], function () {
//    Route::get('/start/{userTest}', [PassTestController::class, 'start']);
//    Route::post('/end/{userTest}', [PassTestController::class, 'end']);
//});
