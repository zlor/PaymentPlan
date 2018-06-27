<?php

namespace App\Models;

use App\Models\Traits\BelongsToInvoicePayment;
use App\Models\Traits\BelongsToPaymentDetail;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SupplierBalanceFlow extends Model
{
    protected $table = 'supplier_balance_flows';

    protected $fillable = [
        'user_id',
        'supplier_id', 'payment_detail_id', 'invoice_payment_id',
        'year', 'month',
        'money', 'date',
        'type', 'kind', 'memo'
    ];

    /**
     * 归属于 供应商所有人
     */
    use BelongsToSupplier, BelongsToPaymentDetail, BelongsToInvoicePayment,
        BelongsToPaymentType;

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

    /**
     * 同步生成收款资金流
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
}
