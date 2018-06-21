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
        'months_pay_cycle', 'terms',
    ];

    /**
     * 归属于 供应商所有人
     */
    use BelongsToSupplierOwner;

    /**
     * 拥有 付款计划、付款明细
     */
    use HasManyPaymentSchedule, HasManyPaymentDetail;


    /**
     * 依据参数猜测出供应商的身份
     *
     * @param array $filter
     * @param bool  $needSave
     *
     * @return Model
     */
    public static function guestOrCreate($filter = [], $needSave = true)
    {
        $query = Supplier::query();
        $new = [];
        if (isset($filter['name']))
        {
            $query->where('name', $filter['name']);
            $new['name'] = $filter['name'];
        }

        if( isset($filter['code']))
        {
            $query->where('code', $filter['code']);
            $new['code'] = $filter['code'];
        }

        if($needSave)
        {
            $supplier = $query->firstOrCreate([], $new);
        }else{
            $supplier = $query->firstOrNew([], $new);
        }

        return $supplier;
    }
}
