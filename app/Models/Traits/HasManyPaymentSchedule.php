<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午9:09
 */

namespace App\Models\Traits;


use App\Models\PaymentSchedule;

trait HasManyPaymentSchedule
{
    /**
     * 拥有 多个付款计划
     *
     * @return mixed
     */
    public function payment_schedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

}