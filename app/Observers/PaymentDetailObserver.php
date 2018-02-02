<?php
namespace App\Observers;

use App\Models\BillPay;
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
        $resPay = empty($paymentDetail->bill_period)?false
                    :BillPay::syncPaymentDetail($paymentDetail);

        return $resPay;
    }

    /**
     * 监听付款删除事件
     *
     * @param PaymentDetail $paymentDetail
     */
    public function deleted(PaymentDetail $paymentDetail)
    {
        $resPay = empty($paymentDetail->bill_period)?false
            :BillPay::syncPaymentDetail($paymentDetail, true);

        return $resPay;
    }

}