<?php
namespace App\Observers;

use App\Models\InvoicePayment;
use App\Models\SupplierBalanceFlow;


class InvoicePaymentObserver
{
    /**
     * 监听应付款发票保存事件
     *
     * @param InvoicePayment $invoicePayment
     */
    public function saved(InvoicePayment $invoicePayment)
    {
        SupplierBalanceFlow::syncInvoice($invoicePayment);
    }

    public function deleted(InvoicePayment $invoicePayment)
    {
        SupplierBalanceFlow::syncInvoice($invoicePayment, true);

    }
}