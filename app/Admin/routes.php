<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    // 首页
    $router->get('/', 'HomeController@index')->name('index');

    // 配置页
    $router->get('/config', 'ConfigController@index')->name('config');

});
