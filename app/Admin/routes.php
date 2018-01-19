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
    $router->get('/envs', 'ConfigController@indexEnvs')->name('envs');

    // 账期总览
    $router->get('/bill/gather', 'HomeController@indexGatherBillPeriod')->name('bill.gather');

    ## 账期设置
    $router->get('/bill/period', 'PeriodController@index')->name('bill.period');
    $router->get('/bill/period/query', 'PeriodController@query')->name('bill.period.query');
    $router->post('/bill/period/set', 'PeriodController@set')->name('bill.period.set');

    ## 付款计划作成
    $router->get('/plan/schedule', 'PaymentController@indexPlan')->name('payment.plan.index');
    $router->patch('/plan/schedule/{id}/update', 'PaymentController@updatePlan')->name('payment.plan.update');
    $router->delete('/plan/schedule/{id}/delete', 'PaymentController@deletePlan')->name('payment.plan.delete');

    $router->get('/plan/schedule/excel', 'PaymentController@indexExcel')->name('payment.plan.excel');
    $router->post('/plan/schedule/upload', 'PaymentController@planUpload')->name('payment.plan.upload');
    $router->get('/plan/schedule/import', 'PaymentController@palnImport')->name('payment.plan.import');

    ## 付款计划审核
    $router->get('/audit/schedule', 'PaymentController@indexAudit')->name('audit.schedule.index');
    $router->patch('/audit/schedule/{id}/confirm', 'PaymentController@auditConfirm')->name('audit.schedule.confirm');
    $router->patch('/audit/schedule/{id}/cancel', 'PaymentController@auditCancel')->name('audit.schedule.cancel');
    $router->patch('/lock/schedule/{id}/confirm', 'PaymentController@lockConfirm')->name('lock.schedule.confirm');
    $router->patch('/lock/schedule/{id}/cancel', 'PaymentController@lockCancel')->name('lock.schedule.cancel');

    ## 付款
    $router->get('/pay/schedule', 'PaymentController@indexPay')->name('pay.schedule.index');
    $router->get('/pay/schedule/{id}/detail', 'PaymentController@indexScheduleDetail')->name('pay.schedule.detail.index');
    $router->patch('/pay/schedule/{id}/detail/{detail_id}/update', 'PaymentController@updateScheduleDetail')->name('pay.schedule.detail.update');
    $router->post('/pay/schedule/{id}/detail', 'PaymentController@storeScheduleDetail')->name('pay.schedule.detail.store');
    $router->delete('/pay/schedule/{id}/detail/{detail_id}', 'PaymentController@deleteScheduleDetail')->name('pay.schedule.detail.delete');


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


// dd(Route::getRoutes());
    // Select 动态加载
    $router->get('/select/payment_schedule/loading', 'SelectController@paymentScheduleLoading')->name('select.payment_schedule.loading');


});
