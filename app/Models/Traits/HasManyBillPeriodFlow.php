<?php
namespace App\Models\Traits;


use App\Models\BillPeriodFlow;

trait HasManyBillPeriodFlow
{
    /**
     * 拥有 多个付款明细
     *
     * @return mixed
     */
    public function bill_period_flows()
    {
        return $this->hasMany(BillPeriodFlow::class);
    }
}