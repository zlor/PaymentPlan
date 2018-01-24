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

class PaymentMateriel extends Model
{
    use SoftDeletes;

    protected $table = 'payment_materiels';

    protected $fillable = [
        'name', 'code', 'memo'
    ];

    /**
     * 拥有 付款计划
     */
    use HasManyPaymentSchedule;


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
        $query = PaymentMateriel::query();
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
            $materiel = $query->firstOrCreate([], $new);
        }else{
            $materiel = $query->firstOrNew([], $new);
        }

        return $materiel;
    }
}
