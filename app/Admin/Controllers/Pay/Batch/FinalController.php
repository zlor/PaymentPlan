<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;

/**
 * Class FinalController
 *
 * 二次核定
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class FinalController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.final.batch',
        'store' => 'final.schedule.store.batch',
    ];
}
