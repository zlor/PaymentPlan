<?php
namespace App\Observers;

use App\Models\BillPay;
use App\Models\BillPeriod;
use App\Models\BillPeriodFlow;
use App\Models\SupplierBalanceFlow;


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
        SupplierBalanceFlow::syncPay($billPay);
    }

    public function deleted(BillPay $billPay)
    {
        BillPeriodFlow::syncPay($billPay, true);
        SupplierBalanceFlow::syncPay($billPay, true);
    }
}