<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $table = 'payment_details';

    protected $fillable = [
        'bill_period_id', 'supplier_id', 'payment_schedule_id', 'user_id',
        'pay_type', 'time', 'money', 'code', 'collecting_company', 'collecting_proof','payment_proof',
        'memo'
    ];

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

    /**
     * 付款计划
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_schedule()
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }

}
