<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

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


Route::post('/auth/login', [UserController::class, 'login']);
Route::get('projects-list', [ProjectController::class,'index']);
Route::get('project-list/{id}', [ProjectController::class,'show']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('project-categories', ProjectCategoryController::class);
    Route::apiResource('users', UserController::class);
    Route::put('/projects/{id}/images-and-banner', [ProjectController::class, 'updateImagesAndBanner'])->name('ProjectController@updateImagesAndBanner');
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/statistics', [ProjectController::class, 'statistics']);
});


