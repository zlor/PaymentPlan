<?php
namespace App\Models\Traits;


use App\Models\BillPay;

trait HasManyBillPay
{
    /**
     * 拥有 多个付款明细
     *
     * @return mixed
     */
    public function bill_pays()
    {
        return $this->hasMany(BillPay::class);
    }
}