<?php
namespace App\Observers;

use App\Models\BillPeriodFlow;


class BillPeriodFlowObserver
{
    /**
     * 监听账期保存事件
     *
     * @param BillPeriodFlow $billPeriodFlow
     *
     * @return bool
     */
    public function saved(BillPeriodFlow $billPeriodFlow)
    {
        $billPeriod = $billPeriodFlow->bill_period()->first();

        if(empty($billPeriod))
        {
            return false;
        }

        // 依据付款计划 执行的付款流水
        $paymentSchedule =  $billPeriodFlow->payment_schedule()->first();

        if(!$paymentSchedule )
        {
            $paymentSchedule->syncFlowMoney();
        }

        return $billPeriod->syncFlowMoney($billPeriodFlow->type);
    }

    public function deleted(BillPeriodFlow $billPeriodFlow)
    {
        $billPeriod = $billPeriodFlow->bill_period()->first();

        if(empty($billPeriod))
        {
            return false;
        }

        // 依据付款计划 执行的付款流水
        $paymentSchedule =  $billPeriodFlow->payment_schedule()->first();

        if(!$paymentSchedule )
        {
            $paymentSchedule->syncFlowMoney();
        }

        return $billPeriod->syncFlowMoney($billPeriodFlow->type);
    }
}