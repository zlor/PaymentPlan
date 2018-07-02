<?php

namespace App\Models;

use App\Models\Traits\BelongsToInvoicePayment;
use App\Models\Traits\BelongsToPaymentDetail;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SupplierBalanceFlow extends Model
{
    protected $table = 'supplier_balance_flows';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'payment_detail_id', 'invoice_payment_id', 'payment_schedule_id','bill_pay_id',
        'year', 'month',
        'money', 'date',
        'type', 'kind', 'memo'
    ];

    /**
     * 归属于 供应商所有人
     */
    use BelongsToSupplier, BelongsToPaymentDetail, BelongsToInvoicePayment,
        BelongsToPaymentType, BelongsToPaymentSchedule;

    /**
     * 应付款减项
     *
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

        $balanceFlow = SupplierBalanceFlow::query()->where('bill_pay_id', $billPay->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($balanceFlow))
            {

                $res = $balanceFlow->delete();
            }
            return $res;
        }

        // 为空，则新建
        if(empty($balanceFlow))
        {
            $balanceFlow = new SupplierBalanceFlow();
        }

        $row = [
            'bill_pay_id' => $billPay->id?:0,
            'payment_detail_id' => $billPay->payment_detail_id?:0,
            'supplier_id'    => $billPay->supplier_id?:0, //**
            'payment_schedule_id' =>$billPay->payment_schedule_id?:0,
            'payment_type_id'   => $billPay->payment_type_id?:0,
            'kind'             =>  $billPay->kind,
            'year'               => $billPay->year ,
            'month'            => $billPay->month,
            'date'             => $billPay->date,
            'money' => -1 * $billPay->money,
            'memo' => $billPay->memo?:'',
            'user_id' => $billPay->user_id?:0,
            'type' => BillPay::MORPH_KEY,
        ];

        $balanceFlow->fill($row);

        $res = $balanceFlow->save();

        return $res;
    }

    /*
     * 应付款增项
     *
     * 同步生成应付款发票
     *
     * @param InvoicePayment $invoicePayment
     * @param bool        $isRemove
     *
     * @return bool|mixed|null
     */
    public static function syncInvoice(InvoicePayment $invoicePayment, $isRemove = false)
    {
        $res = false;

        $balanceFlow = SupplierBalanceFlow::query()->where('invoice_payment_id', $invoicePayment->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($balanceFlow))
            {

                $res = $balanceFlow->delete();
            }
            return $res;
        }

        // 为空，则新建
        if(empty($balanceFlow))
        {
            $balanceFlow = new SupplierBalanceFlow();
        }

        $row = [
            'invoice_payment_id' => $invoicePayment->id?:0,
            'supplier_id'    => $invoicePayment->supplier_id?:0, //**
            'year'               => $invoicePayment->year ,
            'month'            => $invoicePayment->month,
//            'kind'             =>  '',
            'date' => $invoicePayment->date,
            'money' => $invoicePayment->money?:0,
            'memo' => $invoicePayment->memo?:'',
            'user_id' => $invoicePayment->user_id?:0,
            'type' => InvoicePayment::MORPH_KEY,
        ];

        $balanceFlow->fill($row);

        $res = $balanceFlow->save();

        return $res;
    }

    /**
     * 应付款余额初始项
     *
     * 同步到应付款余额
     *
     * && 若已存在初始项，默认跳过不处理
     *
     * @param PaymentSchedule $schedule
     * @param bool $isRemove
     * @param bool $isReset  重新作成初始数据
     *
     * @return bool|mixed|null
     */
    public static function syncInitByPaymentSchedule(PaymentSchedule $schedule, $isRemove = false, $isReset = false)
    {
        $res = false;

        $balanceFlows = SupplierBalanceFlow::query()
            ->where('supplier_id', $schedule->supplier_id)
            ->where('type', 'init')
            ->whereNotIn('payment_schedule_id', [$schedule->id])
            ->get();
        // 是否已存在初始化数据
        if(count($balanceFlows)>0)
        {
            // 重置
            if($isReset)
            {
                foreach ($balanceFlows as $flow)
                {
                    $flow->delete();
                }
            }else{
                // 不允许重置
                return $res;
            }
        }

        $balanceFlow = SupplierBalanceFlow::query()->where('payment_schedule_id', $schedule->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($balanceFlow))
            {

                $res = $balanceFlow->delete();
            }
            return $res;
        }

        $money = $schedule->supplier_balance?:0;
        // 为空，则新建
        if(empty($balanceFlow))
        {
            // 若为新建，在数据为0时，不再生成
            if(intval(100*$money)==0)
            {
                return true;
            }
            $balanceFlow = new SupplierBalanceFlow();
        }

        $billPeriod = $schedule->bill_period;

        $supplier = $schedule->supplier;

        $row = [
            'payment_schedule_id' => $schedule->id?:0,
            'supplier_id'    => $schedule->supplier_id?:0, //**
            'year'               => intval(date('Y', strtotime($billPeriod->month))),
            'month'            => intval(date('m', strtotime($billPeriod->month))),
            'kind'             =>  PaymentSchedule::MORPH_KEY,
            'date' => $billPeriod->time_begin,
            'money' => $schedule->supplier_balance?:0,
            'memo' => "取值自付款计划({$billPeriod->month},{$supplier->name},[{$schedule->plan_man}])",
            'user_id' => 0,
            'type' => 'init',
        ];

        $balanceFlow->fill($row);

        $res = $balanceFlow->save();

        return $res;
    }
}
