<?php

use App\Http\Controllers\ShapefileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('get-lakes', [ShapefileController::class, 'index']);
Route::post('get-feature-info', [ShapefileController::class, 'getFeatureInfo']);
Route::post('search-feature-name', [ShapefileController::class, 'searchFeatureName']);

Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => 'jwt'], function () {
    Route::post('get-authenticated-user', [UserController::class, 'getAuthenticatedUser']);
});

Route::group(['middleware' => ['jwt', 'jwt.role:2']], function () {
    Route::post('update-feature-info', [ShapefileController::class, 'updateFeatureInfo']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
