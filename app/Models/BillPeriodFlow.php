<?php

namespace App\Models;

use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToSupplier;
use Illuminate\Database\Eloquent\Model;

class BillPeriodFlow extends Model
{
    protected $table = 'bill_period_flows';

    protected $fillable = [
        'bill_period_id', 'supplier_id','payment_type_id', 'payment_schedule_id','pay_id','collect_id',
        'type', 'kind', 'date', 'money', 'memo', 'user_id',
    ];

    use BelongsToBillPeriod, BelongsToSupplier, BelongsToPaymentSchedule;

    /**
     * 是否为依据付款计划 执行的支付
     */
    public function isSchedulePay()
    {
        return !empty($this->payment_schedule);
    }

    /**
     * 同步付款资金
     *
     * @param BillPay $billPay
     * @param bool    $isRemove
     *
     * @return bool|mixed|null
     */
    public static function syncPay(BillPay $billPay, $isRemove =false)
    {
        $res = false;

        $billFlow = BillPeriodFlow::query()->where('pay_id', $billPay->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($billFlow))
            {

                $res = $billFlow->delete();
            }
            return $res;
        }

        // 为空，则新建
        if(empty($billFlow))
        {
            $billFlow = new BillPeriodFlow();
        }

        $row = [
            'pay_id' => $billPay->id?:0,
            'bill_period_id' => $billPay->bill_period_id?:0,
            'supplier_id'    => $billPay->supplier_id?:0, //**
            'payment_schedule_id' =>$billPay->payment_schedule_id?:0,
            'payment_type_id'   => $billPay->payment_type_id?:0,
            'kind'             =>  $billPay->kind,
            'date' => $billPay->date,
            'money' => -1 * $billPay->money,
            'memo' => $billPay->memo?:'',
            'user_id' => $billPay->user_id?:0,
            'type' => BillPay::MORPH_KEY,
        ];

        $billFlow->fill($row);

        $res = $billFlow->save();

        return $res;
    }

    /**
     * 同步生成收款资金流
     *
     * @param BillCollect $billCollect
     * @param bool        $isRemove
     *
     * @return bool|mixed|null
     */
    public static function syncCollect(BillCollect $billCollect, $isRemove = false)
    {
        $res = false;

        $billFlow = BillPeriodFlow::query()->where('collect_id', $billCollect->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($billFlow))
            {

                $res = $billFlow->delete();
            }
            return $res;
        }

        // 为空，则新建
        if(empty($billFlow))
        {
            $billFlow = new BillPeriodFlow();
        }

        $row = [
            'collect_id' => $billCollect->id?:0,
            'bill_period_id' => $billCollect->bill_period_id?:0,
            'supplier_id'    => $billCollect->supplier_id?:0, //**
            'kind'             =>  $billCollect->kind,
            'date' => $billCollect->date,
            'money' => $billCollect->money?:0,
            'memo' => $billCollect->memo?:'',
            'user_id' => $billCollect->user_id?:0,
            'type' => BillCollect::MORPH_KEY,
        ];

        $billFlow->fill($row);

        $res = $billFlow->save();

        return $res;
    }
}
