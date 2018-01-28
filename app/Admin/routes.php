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
    $router->get('/bill/{id}/{type_id}/gather', 'HomeController@indexGatherBillPeriod')->name('bill.target.gather');
    $router->get('/bill/gather/{id}', 'BillPeriodController@editCashPool')->name('bill.pool.edit');
    $router->put('/bill/gather/{id}', 'BillPeriodController@updateCashPool')->name('bill.pool.update');

    ## 账期设置
    $router->get('/bill/period', 'PeriodController@index')->name('bill.period.index');
    $router->get('/bill/period/create', 'PeriodController@create')->name('bill.period.create');
    $router->post('/bill/period', 'PeriodController@store')->name('bill.period.store');
    $router->get('/bill/period/{id}/fire', 'PeriodController@fire')->name('bill.period.fire');


    ## 付款计划作成
    $router->get('/plan/schedule', 'Pay\ScheduleController@index')->name('payment.schedule.plan');
    $router->get('/plan/schedule/{id}/edit', 'Pay\ScheduleController@edit')->name('payment.plan.edit');
    $router->put('/plan/schedule/{id}', 'Pay\ScheduleController@update')->name('payment.plan.update');
    $router->delete('/plan/schedule/{id}', 'Pay\ScheduleController@destroy')->name('payment.plan.destroy');
    // // 标记计划完成
    // // 标记需要重新检查
    // $router->patch('/plan/schedule/{id}/confirm', 'Pay\ScheduleController@confirm')->name('payment.plan.confirm');
    // $router->patch('/plan/schedule/{id}/review', 'Pay\ScheduleController@review')->name('payment.plan.review');

    $router->get('/plan/schedule/excel', 'Pay\ExcelController@index')->name('payment.plan.excel');
    $router->post('/plan/schedule/excel', 'Pay\ExcelController@upload')->name('payment.plan.excel.upload');
    $router->post('/plan/schedule/file/{id}/import', 'Pay\ExcelController@import')->name('payment.plan.file.import');
    $router->get('/plan/schedule/file/{id}/download', 'Pay\ExcelController@download')->name('payment.plan.file.download');
    $router->delete('/plan/schedule/file/{id}/delete', 'Pay\ExcelController@remove')->name('payment.plan.file.remove');
    $router->get('/plan/schedule/file/{id}/info', 'Pay\ExcelController@info')->name('payment.plan.file.info');

    ## 付款计划初稿审核
    $router->get('/audit/schedule', 'Pay\AuditController@index')->name('payment.schedule.audit');

    // 初稿核定编辑页面
    $router->get('/audit/schedule/{id}/edit', 'Pay\AuditController@edit')->name('audit.schedule.edit');
    $router->put('/audit/schedule/{id}', 'Pay\AuditController@update')->name('audit.schedule.update');

    ## 付款计划终稿审核
    $router->get('/final/schedule', 'Pay\FinalController@index')->name('payment.schedule.final');
    // 终稿核定编辑页面
    $router->get('/final/schedule/{id}/edit', 'Pay\FinalController@edit')->name('final.schedule.edit');
    $router->put('/final/schedule/{id}', 'Pay\FinalController@update')->name('final.schedule.update');

    ## 付款计划付款核定
    $router->get('/lock/schedule', 'Pay\AuditController@index')->name('payment.schedule.lock');
    $router->get('/lock/schedule/{id}/edit', 'Pay\AuditController@lockEdit')->name('lock.schedule.edit');
    $router->put('/lock/schedule/{id}', 'Pay\AuditController@lockUpdate')->name('lock.schedule.update');

    ## 付款计划进度(用于锁定付款)
    $router->get('/progress/schedule', 'Pay\ProgressController@index')->name('payment.schedule.progress');
    $router->get('/progress/schedule/{id}/edit', 'Pay\ProgressController@edit')->name('progress.schedule.edit');
    $router->put('/progress/schedule/{id}/', 'Pay\ProgressController@update')->name('progress.schedule.update');
    $router->get('/progress/schedule/{id}', 'Pay\ProgressController@view')->name('progress.schedule.view');

    // $router->patch('/audit/schedule/{id}/confirm', 'Pay\AuditController@auditConfirm')->name('audit.schedule.confirm');
    // $router->patch('/audit/schedule/{id}/cancel', 'Pay\AuditController@auditCancel')->name('audit.schedule.cancel');

    // $router->patch('/final/schedule/{id}/confirm', 'Pay\AuditController@finalConfirm')->name('final.schedule.confirm');
    // $router->patch('/final/schedule/{id}/cancel', 'Pay\AuditController@finalCancel')->name('final.schedule.cancel');

    // $router->patch('/lock/schedule/{id}/confirm', 'Pay\AuditController@lockConfirm')->name('lock.schedule.confirm');
    // $router->patch('/lock/schedule/{id}/cancel', 'Pay\AuditController@lockCancel')->name('lock.schedule.cancel');

    ## 付款引导界面
    $router->get('/pay/schedule', 'Pay\DetailController@index')->name('payment.schedule.pay');

    // 提交付款信息
    $router->get('/pay/schedule/detail', 'Pay\DetailController@detail')->name('pay.schedule.detail');
    $router->put('/pay/schedule/detail/{id}', 'Pay\DetailController@update')->name('pay.schedule.detail.update');
    $router->post('/pay/schedule/detail', 'Pay\DetailController@store')->name('pay.schedule.detail.store');
    $router->delete('/pay/schedule/detail/{id}', 'Pay\DetailController@destroy')->name('pay.schedule.detail.destroy');
    $router->get('/pay/schedule/detail/{id}/info', 'Pay\DetailController@info')->name('pay.schedule.detail.info');

    // 获取付款相关信息




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
