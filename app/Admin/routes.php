<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => array_merge(config('admin.route.middleware'), ['env.user']),
], function (Router $router) {

    // 首页
    $router->get('/', 'HomeController@index')->name('index');

    // 配置页
    $router->get('/envs', 'ConfigController@indexEnvs')->name('envs');

    // 账期总览
    $router->get('/bill/gather', 'HomeController@indexGatherBillPeriod')->name('bill.gather');
    $router->get('/bill/{id}/gather', 'HomeController@indexGatherBillPeriod')->name('bill.target.gather');

    ## 账期设置
    $router->get('/bill/period', 'PeriodController@index')->name('bill.period');
    $router->get('/bill/period/query', 'PeriodController@query')->name('bill.period.query');
    $router->post('/bill/period/set', 'PeriodController@set')->name('bill.period.set');

    ## 付款计划作成
    $router->get('/plan/schedule', 'Pay\ScheduleController@index')->name('payment.plan.index');
    $router->get('/plan/schedule/{id}/edit', 'Pay\ScheduleController@edit')->name('payment.plan.edit');
    $router->put('/plan/schedule/{id}', 'Pay\ScheduleController@update')->name('payment.plan.update');
    $router->delete('/plan/schedule/{id}', 'Pay\ScheduleController@destroy')->name('payment.plan.destroy');
    // // 标记计划完成
    // // 标记需要重新检查
    // $router->patch('/plan/schedule/{id}/confirm', 'Pay\ScheduleController@confirm')->name('payment.plan.confirm');
    // $router->patch('/plan/schedule/{id}/review', 'Pay\ScheduleController@review')->name('payment.plan.review');

    $router->get('/plan/schedule/excel', 'Pay\ExcelController@index')->name('payment.plan.excel');
    $router->post('/plan/schedule/file/upload', 'Pay\ExcelController@upload')->name('payment.plan.file.upload');
    $router->post('/plan/schedule/file/{id}/import', 'Pay\ExcelController@import')->name('payment.plan.file.import');
    $router->get('/plan/schedule/file/{id}/download', 'Pay\ExcelController@download')->name('payment.plan.file.download');
    $router->delete('/plan/schedule/file/{id}/delete', 'Pay\ExcelController@remove')->name('payment.plan.file.remove');
    $router->get('/plan/schedule/file/{id}/info', 'Pay\ExcelController@info')->name('payment.plan.file.info');

    ## 付款计划审核
    $router->get('/audit/schedule', 'Pay\AuditController@index')->name('payment.audit.index');
    $router->patch('/audit/schedule/{id}/confirm', 'Pay\AuditController@auditConfirm')->name('audit.schedule.confirm');
    $router->patch('/audit/schedule/{id}/cancel', 'Pay\AuditController@auditCancel')->name('audit.schedule.cancel');
    $router->patch('/final/schedule/{id}/confirm', 'Pay\AuditController@finalConfirm')->name('final.schedule.confirm');
    $router->patch('/final/schedule/{id}/cancel', 'Pay\AuditController@finalCancel')->name('final.schedule.cancel');
    $router->patch('/lock/schedule/{id}/confirm', 'Pay\AuditController@lockConfirm')->name('lock.schedule.confirm');
    $router->patch('/lock/schedule/{id}/cancel', 'Pay\AuditController@lockCancel')->name('lock.schedule.cancel');

    ## 付款
    $router->get('/pay/schedule', 'Pay\DetailController@index')->name('pay.schedule.index');
    $router->get('/pay/schedule/detail/{id}', 'Pay\DetailController@indexScheduleDetail')->name('pay.schedule.detail.index');
    $router->patch('/pay/schedule/detail/{id}/update', 'Pay\DetailController@updateScheduleDetail')->name('pay.schedule.detail.update');
    $router->post('/pay/schedule/detail', 'Pay\DetailController@storeScheduleDetail')->name('pay.schedule.detail.store');
    $router->delete('/pay/schedule/detail/{id}', 'Pay\DetailController@deleteScheduleDetail')->name('pay.schedule.detail.delete');


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
