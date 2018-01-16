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

    // 基础档案
    // --  供应商
    $router->resource('/base/suppliers', 'SupplierController');
    // --  账期档案
    $router->resource('/base/bill_periods', 'BillPeriodController');


});
