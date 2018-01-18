<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午9:09
 */

namespace App\Models\Traits;


use App\Models\PaymentDetail;

trait HasManyPaymentDetail
{
    /**
     * 拥有 多个付款明细
     *
     * @return mixed
     */
    public function payment_details()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}