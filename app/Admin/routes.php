<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('memberships', MembershipController::class);
    $router->resource('skins', SkinController::class);
    $router->resource('exchanges', ExchangeController::class);
    $router->resource('profit-per-hours', ProfitPerHourController::class);
    $router->resource('category', CategoryController::class);
    $router->resource('card',CardController::class);
    $router->resource('card-profit',CardProfitController::class);
    $router->resource('earn',EarnController::class);
});
