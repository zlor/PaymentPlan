<?php
namespace App\Models\Traits;


use App\Models\InvoicePayment;

trait HasManyInvoicePayment
{
    /**
     * 拥有 多个供应商账户流水明细
     *
     * @return mixed
     */
    public function invoice_payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
}