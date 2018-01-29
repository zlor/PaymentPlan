<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;

/**
 * Class FrozeController
 *
 * 冻结付款
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class FrozeController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.froze.batch',
        'store' => 'froze.schedule.store.batch',
    ];
}
