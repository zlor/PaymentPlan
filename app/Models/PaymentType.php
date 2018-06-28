<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use App\Models\Traits\HasManyBillPay;
use App\Models\Traits\HasManyPaymentSchedule;
use App\Models\Traits\HasManySupplier;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use SoftDeletes;

    use CommonOptions;

    protected $table = 'payment_types';

    protected $fillable = [
        'name', 'code', 'memo',
        'is_plan', 'is_closed', 'parent_id'
    ];

    /**
     * 拥有 付款计划
     */
    use HasManyPaymentSchedule,HasManySupplier;

    /**
     * 拥有 付款明细
     */
    use HasManyBillPay;

    /**
     * 限制查询只包括计划需要的记录。
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSchedule($query)
    {
        return $query->where('is_plan', 1);
    }


    public function getSuppliersCountAttribute()
    {
        return empty($this->suppliers)?0:$this->suppliers->count();
    }

}
