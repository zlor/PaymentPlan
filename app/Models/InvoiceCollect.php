<?php

namespace App\Models;


use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToPaymentDetail;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceCollect
 *
 * 应付发票
 *
 * @package App\Models
 */
class InvoiceCollect extends Model
{
    use CommonOptions;

    protected $table = 'invoice_collects';

    /**
     * reg_ex == ''
     * @var array
     */
    protected $fillable = [
        'client_id',
        'payment_type_id',
        'user_id',
        'payment_detail_id',
        'title', 'code', 'date',
        'year', 'month', 'lay_month',
        'money', 'money_paid',
        'memo'
    ];

    use  BelongsToAdministrator, BelongsToPaymentDetail, BelongsToPaymentType;


}
