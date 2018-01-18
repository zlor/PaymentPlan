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
    // --  供应商-所有人
    $router->resource('/base/supplier_owners', 'SupplierOwnerController');
    // --  账期档案
    $router->resource('/base/bill_periods', 'BillPeriodController');
    // -- 付款分类档案
    $router->resource('/base/bill/payment_types', 'PaymentTypeController');
    // -- 付款物料档案
    $router->resource('/base/bill/payment_materiels', 'PaymentMaterielController');
    // --  付款计划档案
    $router->resource('/base/bill/payment_schedules', 'PaymentScheduleController');
    // --  付款明细档案
    $router->resource('/base/bill/payment_details', 'PaymentDetailController');



    // Select 动态加载
    $router->get('/select/payment_schedule/loading', 'SelectController@paymentScheduleLoading')->name('select.payment_schedule.loading');


});
