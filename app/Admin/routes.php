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

    // 账期总览
    $router->get('/bill/current', 'HomeController@bill_current')->name('bill_period');

    // 基础档案
    // --  供应商
    $router->resource('/base/suppliers', 'SupplierController');
    // --  账期档案
    $router->resource('/base/bill_periods', 'BillPeriodController');
    // --  付款计划档案
    $router->resource('/base/bill/payment_schedules', 'PaymentScheduleController');
    // --  付款明细档案
    $router->resource('/base/bill/payment_details', 'PaymentDetailController');


});
