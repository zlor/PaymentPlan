<?php

namespace App\Models;

use App\Models\Traits\BelongsToSupplierOwner;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'name', 'code', 'logo',
        'contact', 'address', 'tel',
        'head', 'supplier_owner_id',
    ];

    /**
     * 归属于 供应商所有人
     */
    use BelongsToSupplierOwner;

    /**
     * 拥有 付款计划、付款明细
     */
    use HasManyPaymentSchedule, HasManyPaymentDetail;
}
