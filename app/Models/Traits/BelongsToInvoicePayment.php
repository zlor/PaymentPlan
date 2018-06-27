<?php
namespace App\Models\Traits;

use App\Models\InvoicePayment;
use Closure;

trait BelongsToInvoicePayment
{
    /**
     * 应付款发票
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inovice_payment()
    {
        return $this->belongsTo(InvoicePayment::class, 'invoice_payment_id');
    }

    /**
     * 应付款发票-发票号码
     * @return string
     */
    public function getInvoicePaymentCode()
    {
        $invoice_payment = $this->inovice_payment()->first();
        return empty($invoice_payment) ? '' : $invoice_payment->code;
    }

    /**
     * 获取应付款发票-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getInvoicePaymentOptions(Closure $callable = null)
    {
        $query = InvoicePayment::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('code', 'id');
    }
}