<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\SkinController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\ProfitPerHourController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('users/check', [UserController::class, 'index']);
Route::get('memberships', [MembershipController::class, 'index']);
Route::get('skins', [SkinController::class, 'index']);
Route::get('exchanges', [ExchangeController::class, 'index']);
Route::get('profit-per-hours', [ProfitPerHourController::class, 'index']);
