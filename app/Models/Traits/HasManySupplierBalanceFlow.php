<?php
namespace App\Models\Traits;


use App\Models\SupplierBalanceFlow;

trait HasManySupplierBalanceFlow
{
    /**
     * 拥有 多个供应商账户流水明细
     *
     * @return mixed
     */
    public function supplier_balance_flows()
    {
        return $this->hasMany(SupplierBalanceFlow::class);
    }
}