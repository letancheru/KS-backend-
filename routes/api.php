<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectCategoryNameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MailConfigController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\ServiceController;

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
Route::get('partners-list', [PartnerController::class,'index']);
Route::get('teams-list', [TeamController::class,'index']);
Route::get('project-list/{id}', [ProjectController::class,'show']);
Route::get('password', [UserController::class,'password']);
Route::post('contacts', [ContactController::class, 'store']);
Route::get('contacts', [ContactController::class, 'index']);

Route::get('service', [ServiceController::class, 'index']);

Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::delete('contacts/{id}', [ContactController::class, 'show']);
Route::apiResource('contacts', ContactController::class);
Route::post('/mail-config', [MailConfigController::class, 'store']);
Route::get('/mail-config', [MailConfigController::class, 'getMailConfig']);
Route::post('/business-setup', [BusinessSettingController::class, 'store']);
Route::get('/business-setup', [BusinessSettingController::class, 'index']);
Route::apiResource('project-category-name', ProjectCategoryNameController::class);
Route::middleware('auth:api')->group(function () {
    Route::post('service', [ServiceController::class, 'store']);
    Route::get('service/{id}', [ServiceController::class, 'show']);
    Route::put('service/{id}', [ServiceController::class, 'update']);
    Route::delete('service/{id}', [ServiceController::class, 'destroy']);
    Route::put('service/{id}/images-and-banner', [ServiceController::class, 'updateImagesAndBanner']);
    Route::apiResource('partners', PartnerController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('project-categories', ProjectCategoryController::class);
    Route::apiResource('users', UserController::class);
    Route::put('/projects/{id}/images-and-banner', [ProjectController::class, 'updateImagesAndBanner'])->name('ProjectController@updateImagesAndBanner');
    Route::put('/partners/{id}/banner', [PartnerController::class, 'updateBanner'])->name('PartnerController@updateBanner');
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/statistics', [ProjectController::class, 'statistics']);
    Route::apiResource('teams', TeamController::class);
    Route::put('/teams/{id}/image', [TeamController::class, 'updateImage'])->name('TeamController@updateImage');

});


