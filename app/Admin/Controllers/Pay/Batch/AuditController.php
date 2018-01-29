<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;

/**
 * Class AuditController
 *
 * 一次核定
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class AuditController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.audit.batch',
        'store' => 'audit.schedule.store.batch',
    ];
}
