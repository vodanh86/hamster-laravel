<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserCardController;
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
//user
Route::post('users/check', [UserController::class, 'index']);
Route::post('users/login', [UserController::class, 'store']);
Route::get('users/infor/{id}', [UserController::class, 'userInfor']);
Route::post('users/update-revenue', [UserController::class, 'updateRevenue']);

//memebership
Route::get('memberships/all', [MembershipController::class, 'index']);
Route::post('memberships/get-by-user', [MembershipController::class, 'getByUser']);

//skins
Route::get('skins', [SkinController::class, 'index']);

//profit per hour
Route::get('profit-per-hours', [ProfitPerHourController::class, 'index']);
Route::post('profit-per-hours/get-by-user-and-exchange', [ProfitPerHourController::class, 'getByUserAndExchange']);

//exchange
Route::get('exchanges', [ExchangeController::class, 'index']);
Route::post('exchanges/get-by-user', [ExchangeController::class, 'getByUser']);
Route::post('exchanges/update-by-user', [ExchangeController::class, 'updateExchangeByUser']);

//category
Route::get('category/all', [CategoryController::class, 'index']);

//user-card
Route::post('user-card/buy', [UserCardController::class, 'store']);
Route::post('user-card/get-by-user-and-exchange-and-category', [UserCardController::class, 'getCardsByUserIdAndCategoryId']);

//card
Route::post('card/get-by-category', [CardController::class, 'getByCategory']);
