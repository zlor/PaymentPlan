<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use App\Models\Traits\HasManyPaymentDetail;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $table = 'payment_schedules';

    use CommonOptions;
    /**
     * 归属于 账期、用户、供应商、物料、类型
     */
    use BelongsToBillPeriod, BelongsToAdministrator, BelongsToSupplier, BelongsToPaymentMateriel, BelongsToPaymentType;

    /**
     * 拥有 付款明细
     */
    use HasManyPaymentDetail;


    /**
     * 在 select中显示的字段
     * @return string
     */
    public function select_text()
    {
        return $this->bill_period_name .'_'. $this->payment_type_name .' ('.$this->supplier_name.')';
    }

    /**
     * 已付金额
     * @return mixed
     */
    public function getPaidMoneyAttribute()
    {
        return $this->cash_paid + $this->acceptance_paid;
    }
}
