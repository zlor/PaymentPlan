<?php
namespace App\Observers;

use App\Models\BillPeriod;


class BillPeriodObserver
{
    /**
     * 监听账期保存事件
     *
     * @param BillPeriod $billPeriod
     */
    public function saved(BillPeriod $billPeriod)
    {
    }
}