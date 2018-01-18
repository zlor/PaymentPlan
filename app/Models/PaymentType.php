<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\HasManyPaymentSchedule;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use SoftDeletes;

    protected $table = 'payment_types';

    protected $fillable = [
        'name', 'code', 'memo'
    ];

    /**
     * 拥有 付款计划
     */
    use HasManyPaymentSchedule;
}
