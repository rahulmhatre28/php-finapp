<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\InhouseController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\RoleController;
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
Route::get('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/test', [AuthController::class, 'test']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth.info'])->group(function () {

    Route::get('/role', [RoleController::class, 'index']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::put('/role', [RoleController::class, 'update']);
    Route::get('/menu', [MenuController::class, 'index']);

    Route::post('/user', [UserController::class, 'insert']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/getchild', [UserController::class, 'getchild']);
    Route::get('/user/{id}', [UserController::class, 'getById']);
    Route::put('/user/{id}', [UserController::class, 'update']);

    Route::post('/channel', [ChannelController::class, 'insert']);
    Route::get('/channel', [ChannelController::class, 'index']);
    Route::get('/channel/dropdown', [ChannelController::class, 'dropdown']);
    Route::get('/channel/{id}', [ChannelController::class, 'getById']);
    Route::put('/channel/{id}', [ChannelController::class, 'update']);
    Route::delete('/channel/{id}', [ChannelController::class, 'delete']);

    Route::post('/inhouse', [InhouseController::class, 'insert']);
    Route::get('/inhouse', [InhouseController::class, 'index']);
    Route::get('/inhouse/{id}', [InhouseController::class, 'getById']);
    Route::put('/inhouse/{id}', [InhouseController::class, 'update']);
    Route::delete('/inhouse/{id}', [InhouseController::class, 'delete']);

    Route::post('/loan', [LoanController::class, 'insert']);
    Route::get('/loan', [LoanController::class, 'index']);
    Route::get('/loan/{id}', [LoanController::class, 'getById']);
    Route::put('/loan/{id}', [LoanController::class, 'update']);
    Route::delete('/loan/{id}', [LoanController::class, 'delete']);
    Route::post('/loan/assign', [LoanController::class, 'assignLender']);

    Route::get('mom/getByParams',[MomController::class,'getByParams']);

    Route::get('location/getByParams',[LocationController::class,'getByParams']);

});