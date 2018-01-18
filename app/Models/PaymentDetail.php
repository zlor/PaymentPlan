<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToSupplier;
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
     * 归属于 账期、用户、供应商
     */
    use BelongsToBillPeriod, BelongsToAdministrator, BelongsToSupplier;

    /**
     * 归属于 付款计划
     */
    use BelongsToPaymentSchedule;


    /**
     * 付款方式备选
     * @return array
     */
    public static function getPayTypeOptions()
    {
        return trans_options('pay_type', ['cash', 'acceptance'], 'payment.detail');
    }

}
