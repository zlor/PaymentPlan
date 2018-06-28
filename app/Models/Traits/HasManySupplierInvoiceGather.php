<?php
namespace App\Models\Traits;


use App\Models\SupplierInvoiceGather;

trait HasManySupplierInvoiceGather
{
    /**
     * 拥有 多个供应商账户流水明细
     *
     * @return mixed
     */
    public function supplier_invoice_gathers()
    {
        return $this->hasMany(SupplierInvoiceGather::class);
    }
}