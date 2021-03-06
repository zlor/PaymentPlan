<?php
namespace App\Models\Traits;

use Closure;
use App\Models\PaymentSchedule;

trait BelongsToPaymentSchedule
{
    /**
     * 付款计划
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_schedule()
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }

    /**
     * 付款计划-流水号
     * @return string
     */
    public function getPaymentScheduleNameAttribute()
    {
        $paymentSchedule = $this->payment_schedule()->first();

        return empty($paymentSchedule) ? '' : $paymentSchedule->name;
    }

    /**
     * 付款类型ID
     * @return int|mixed
     */
    public function getPaymentTypeIdAttribute()
    {
        $paymentSchedule = $this->payment_schedule()->first();
        return empty($paymentSchedule) ? 0 : $paymentSchedule->payment_type_id;
    }

    /**
     * 获取付款计划-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentScheduleOptions(Closure $callable = null)
    {
        $query = PaymentSchedule::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('name', 'id');
    }
}