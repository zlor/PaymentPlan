<?php
namespace App\Observers;

use App\Models\BillCollect;
use App\Models\BillPeriodFlow;


class BillCollectObserver
{
    /**
     * 监听账期保存事件
     *
     * @param BillCollect $billCollect
     */
    public function saved(BillCollect $billCollect)
    {
        BillPeriodFlow::syncCollect($billCollect);
    }

    public function deleted(BillCollect $billCollect)
    {
        BillPeriodFlow::syncCollect($billCollect, true);
    }
}