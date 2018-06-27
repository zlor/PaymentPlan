<?php

namespace App\Models;


use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToPaymentDetail;
use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierInvoiceGather
 *
 * 应付发票
 *
 * @package App\Models
 */
class SupplierInvoiceGather extends Model
{
    use CommonOptions;

    const  MORPH_KEY = 'invoice_gather';

    protected $table = 'invoice_gather_shoots';

    /**
     * reg_ex == ''
     * @var array
     */
    protected $fillable = [
        'year', 'month', 'date',
        'supplier_id', 'payment_schedule_id',
        'money'
    ];

    use  BelongsToSupplier;

    public function getYearAttribute()
    {
        return empty($this->year)?date('Y', strtotime($this->date)):$this->year;
    }

    public function getMonthAttribute()
    {
        return empty($this->month)?date('m', strtotime($this->date)):$this->month;
    }
}
