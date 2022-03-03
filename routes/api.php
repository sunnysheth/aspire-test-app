<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ApplicationController;

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

// authentication api routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('user.register');
    Route::post('/login', 'login')->name('user.login');
    Route::post('/logout', 'logout')->middleware('auth:api');
});

// loan application routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::controller(ApplicationController::class)->group(function () {
        Route::post('loan/apply', 'apply')->name('loan.apply');
        Route::post('loan/{id}/approve', 'approve')->name('loan.approve');
        Route::post('loan/{id}/reject', 'reject')->name('loan.reject');
        Route::post('loan/{id}/pay-emi', 'payLoanEMI')->name('loan.receive.payment');
    });
});