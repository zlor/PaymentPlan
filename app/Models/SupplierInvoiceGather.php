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

    public function getYearAttribute($value)
    {
        return empty($value)?date('Y', strtotime($this->date)):$value;
    }

    public function getMonthAttribute($value)
    {
        return empty($value)?date('m', strtotime($this->date)):$value;
    }
}
