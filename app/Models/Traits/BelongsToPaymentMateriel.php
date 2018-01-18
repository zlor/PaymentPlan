<?php
namespace App\Models\Traits;

use App\Models\PaymentMateriel;
use Closure;

trait BelongsToPaymentMateriel
{
    /**
     * 付款物料
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_materiel()
    {
        return $this->belongsTo(PaymentMateriel::class, 'payment_materiel_id');
    }

    /**
     * 付款物料-名称
     * @return string
     */
    public function getPaymentMaterielNameAttribute()
    {
        return empty($this->payment_materiel) ? '' : $this->payment_materiel->name;
    }

    /**
     * 获取付款物料-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentMaterielOptions(Closure $callable = null)
    {
        $query = PaymentMateriel::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, [$query]);
        }

        return $query->get()->pluck('name', 'id');
    }
}