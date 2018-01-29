<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;

/**
 * Class LockController
 *
 * 锁定计划，敲定应付款
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class LockController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.lock.batch',
        'store' => 'lock.schedule.store.batch',
    ];
}
