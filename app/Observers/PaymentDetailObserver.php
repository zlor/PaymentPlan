<?php
namespace App\Observers;

use App\Models\PaymentDetail;


class PaymentDetailObserver
{
    /**
     * 监听付款保存事件
     *
     * @param PaymentDetail $paymentDetail
     */

    public function saved(PaymentDetail $paymentDetail)
    {
        $res1 = empty($paymentDetail->bill_period) ? false
                    : $paymentDetail->bill_period->syncMoney();
        $res2 = empty($paymentDetail->payment_schedule) ? false
                        : $paymentDetail->payment_schedule->syncMoney();
        return $res1 && $res2;
    }

    /**
     * 监听付款删除事件
     *
     * @param PaymentDetail $paymentDetail
     */
    public function deleted(PaymentDetail $paymentDetail)
    {
        $res1 = empty($paymentDetail->bill_period) ? false
                    : $paymentDetail->bill_period->syncMoney();
        $res2 = empty($paymentDetail->payment_schedule) ? false
                    : $paymentDetail->payment_schedule->syncMoney();
        return $res1 && $res2;
    }

}