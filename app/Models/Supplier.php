<?php

namespace App\Models;

use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplierOwner;
use App\Models\Traits\HasManyInvoicePayment;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentSchedule;
use App\Models\Traits\HasManySupplierBalanceFlow;
use App\Models\Traits\HasManySupplierInvoiceGather;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'name', 'code', 'logo',
        'contact', 'address', 'tel',
        'charge_man',
        'head', 'supplier_owner_id',
        'months_pay_cycle', 'terms',
    ];

    /**
     * 归属于 供应商所有人
     */
    use BelongsToSupplierOwner, BelongsToPaymentMateriel, BelongsToPaymentType;

    /**
     * 拥有 付款计划、付款明细
     */
    use HasManyPaymentSchedule, HasManyPaymentDetail, HasManySupplierBalanceFlow, HasManySupplierInvoiceGather, HasManyInvoicePayment;


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

    public function getBalanceMoneyAttribute()
    {
        $money = 0;

        if(!empty($this->supplier_balance_flows))
        {
            $money = $this->supplier_balance_flows->sum('money');
        }
        return $money;
    }

    /**
     * 按截止月来合计账户余额
     *
     * @param $year
     * @param $month
     * @return int
     */
    public function getBalanceMoneyMonth($year, $month)
    {
        $money = 0;

        if(!empty($this->supplier_balance_flows))
        {
            $money = $this->supplier_balance_flows->where('(100*year+month)', '<=', 100*$year+$month)->sum('money');
        }

        return $money;
    }

    public function getInvoiceGatherMonth($year, $month)
    {
        $money = 0;

        if(!empty($this->supplier_invoice_gathers))
        {
            $money = $this->supplier_invoice_gathers->where('year', $year)->where('month', $month)->sum('month');
        }

        return $money;
    }
}
