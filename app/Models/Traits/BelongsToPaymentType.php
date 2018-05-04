<?php
namespace App\Models\Traits;

use App\Models\PaymentType;
use Closure;
use Illuminate\Queue\InvalidPayloadException;

trait BelongsToPaymentType
{
    /**
     * 付款物料
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    /**
     * 付款物料-名称
     * @return string
     */
    public function getPaymentTypeNameAttribute()
    {
        return empty($this->payment_type) ? '' : $this->payment_type->name;
    }

    /**
     * 获取付款(计划)类型-备选
     * @param Closure|null $callable
     * @return \Illuminate\Support\Collection|mixed
     */
    public static function getPaymentScheduleTypeOptions(Closure $callable = null)
    {
        $query = PaymentType::query()->schedule();

        if(! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('name', 'id');
    }

    /**
     * 获取付款物料-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentTypeOptions(Closure $callable = null)
    {
        $query = PaymentType::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('name', 'id');
    }

    /**
     * 获取付款物料Excel_Sheet-备选
     *
     * @param Closure|null $callable
     *
     * @return \Illuminate\Support\Collection|mixed
     */
    public static function getPaymentTypeSheetOptions(Closure $callable = null)
    {
        $query = PaymentType::query();

        if(! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->where('map_sheet', true)->get()->pluck('sheet_slug', 'id');
    }
}