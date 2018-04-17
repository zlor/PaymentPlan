<?php

namespace App\Models;


use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\CommonOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCycle extends Model
{
    use SoftDeletes;

    use CommonOptions;

    protected $table = 'payment_cycles';

    /**
     * reg_ex == ''
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'bill_period_id',
        'payment_schedule_id',
        'payment_type_id',
        'slug',
        'reg_ex',
        'day_num', 'month_num',
        'is_checked',
        'is_closed',
        'memo',
    ];

    use  BelongsToPaymentType;


    /**
     *
     */
}
