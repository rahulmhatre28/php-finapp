<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\InhouseController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DsaUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\YtdController;
use App\Http\Controllers\PaymentController;
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
Route::get('/loan/download/{id}', [LoanController::class, 'download']);

Route::middleware(['auth.info'])->group(function () {

    Route::get('/role', [RoleController::class, 'index']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::put('/role', [RoleController::class, 'update']);
    Route::get('/role/{id}', [RoleController::class, 'getById']);
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/menuAccess/{code}', [MenuController::class, 'menuAccess']);
    Route::get('/menu/list', [MenuController::class, 'menuListByRole']);

    Route::post('/user', [UserController::class, 'insert']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/getchild', [UserController::class, 'getchild']);
    Route::get('/user/ddl', [UserController::class, 'ddl']);
    Route::get('/user/{id}', [UserController::class, 'getById']);
    Route::put('/user/{id}', [UserController::class, 'update']);

    Route::post('/channel', [ChannelController::class, 'insert']);
    Route::get('/channel', [ChannelController::class, 'index']);
    Route::get('/channel/banks', [ChannelController::class, 'banks']);
    Route::get('/channel/dropdown', [ChannelController::class, 'dropdown']);
    Route::get('/channel/borrowerdropdown', [ChannelController::class, 'borrowerdropdown']);
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
    Route::post('/loan/update/{id}', [LoanController::class, 'update']);
    Route::delete('/loan/{id}', [LoanController::class, 'delete']);
    Route::post('/loan/assign', [LoanController::class, 'assignLender']);
    Route::post('/loan/assignperson', [LoanController::class, 'assignPerson']);
    Route::post('/loan/disbursed', [LoanController::class, 'disbursed']);
    Route::get('/loan/disbursed/{id}', [LoanController::class, 'getDisbursedDetails']);

    Route::get('mom/getByParams',[MomController::class,'getByParams']);

    Route::get('location/getByParams',[LocationController::class,'getByParams']);

    Route::get('/bank/list', [BankController::class, 'list']);
    Route::get('/bank/lenders', [BankController::class, 'lenders']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/dsa_user_upload',[DsaUserController::class,'upload']);
    Route::get('/batch/{id}',[DsaUserController::class,'batch']);
    Route::get('/batch_progress',[DsaUserController::class,'batchInProgress']);
    
    Route::post('/ytd_upload',[YtdController::class,'upload']);
    Route::post('/daily_upload',[DailyController::class,'upload']);
    
    Route::get('/report',[ReportController::class,'index']);

    Route::get('/payment', [PaymentController::class, 'index']);
    Route::post('/payment', [PaymentController::class, 'insert']);
    Route::put('/payment', [PaymentController::class, 'update']);
    Route::get('/payment/{loan_id}', [PaymentController::class, 'getByLoanId']);
    // Route::post('/payment', [PaymentController::class, 'insert']);
    // Route::put('/payment', [PaymentController::class, 'update']);

});