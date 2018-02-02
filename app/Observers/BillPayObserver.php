<?php
namespace App\Observers;

use App\Models\BillPay;
use App\Models\BillPeriodFlow;


class BillPayObserver
{
    /**
     * 监听账期保存事件
     *
     * @param BillPay $billPay
     */
    public function saved(BillPay $billPay)
    {
        BillPeriodFlow::syncPay($billPay);
    }

    public function deleted(BillPay $billPay)
    {
        BillPeriodFlow::syncPay($billPay, true);
    }
}