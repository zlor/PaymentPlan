<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $table = 'payment_schedules';

    /**
     * 账期
     */
    public function bill_period()
    {
        return $this->belongsTo(BillPeriod::class, 'bill_period_id');
    }

    /**
     * 操作人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }

    /**
     * 供应商
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
