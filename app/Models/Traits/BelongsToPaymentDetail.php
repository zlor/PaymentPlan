<?php
namespace App\Models\Traits;

use App\Models\PaymentDetail;
use Closure;

trait BelongsToPaymentDetail
{
    /**
     * 付款计划
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_detail()
    {
        return $this->belongsTo(PaymentDetail::class, 'payment_detail_id');
    }

    /**
     * 付款计划-流水号
     * @return string
     */
    public function getPaymentDetailCode()
    {
        $payment_detail = $this->payment_detail()->first();
        return empty($payment_detail) ? '' : $payment_detail->code;
    }

    /**
     * 获取付款计划-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentDetailOptions(Closure $callable = null)
    {
        $query = PaymentDetail::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('code', 'id');
    }
}