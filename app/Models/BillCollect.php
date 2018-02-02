<?php

namespace App\Models;

use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToSupplier;
use Illuminate\Database\Eloquent\Model;

class BillCollect extends Model
{
    protected $table = 'bill_collects';

    protected $fillable = [
        'bill_period_id', 'supplier_id',
        'kind', 'date', 'money', 'code', 'memo',
        'acceptance_date', 'acceptance_fee','user_id'
    ];

    const MORPH_KEY = 'collect';


    use BelongsToBillPeriod, BelongsToSupplier;

    /**
     * 账期激活中的, 付款计划允许付款的
     * @return bool
     */
    public function allowEdit()
    {
        $billPeriod = $this->bill_period()->first();

        return empty($billPeriod)?false:$billPeriod->isActive();
    }
}
