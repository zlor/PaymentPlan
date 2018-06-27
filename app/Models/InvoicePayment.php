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
 * Class InvoicePayment
 *
 * 应付发票
 *
 * @package App\Models
 */
class InvoicePayment extends Model
{
    use CommonOptions;

    const  MORPH_KEY = 'invoice';

    protected $table = 'invoice_payments';

    /**
     * reg_ex == ''
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'payment_type_id',
        'payment_materiel_id',
        'user_id',
        'payment_detail_id',
        'title', 'code', 'date',
        'year', 'month', 'lay_month',
        'money', 'money_paid',
        'materiel',
        'payment_terms',
        'memo'
    ];

    use  BelongsToSupplier, BelongsToAdministrator, BelongsToPaymentDetail, BelongsToPaymentType;

    use BelongsToPaymentMateriel;


    public function getYearAttribute()
    {
        return empty($this->year)?date('Y', strtotime($this->date)):$this->year;
    }

    public function getMonthAttribute()
    {
        return empty($this->month)?date('m', strtotime($this->date)):$this->month;
    }
}
