<?php

namespace App\Models;

use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentDetail;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use Illuminate\Database\Eloquent\Model;

class BillPay extends Model
{
    use CommonOptions;

    protected $table = 'bill_pays';

    protected $fillable = [
        'bill_period_id', 'supplier_id', 'payment_schedule_id', 'payment_type_id', 'payment_detail_id',
        'kind', 'date', 'money', 'code', 'company', 'memo',
        'acceptance_date', 'acceptance_fee', 'user_id',
    ];

    const MORPH_KEY = 'pay';

    use BelongsToBillPeriod, BelongsToSupplier, BelongsToPaymentSchedule, BelongsToPaymentType, BelongsToPaymentDetail;

    /**
     * 账期激活中的, 付款计划允许付款的
     * @return bool
     */
    public function allowEdit()
    {
        $billPeriod = $this->bill_period()->first();

        return empty($billPeriod)?false:$billPeriod->isActive();
    }


    /**
     * 同步付款计划明细数据
     *
     * @param PaymentDetail $paymentDetail
     * @param bool          $isRemove
     *
     * @return bool
     */
    public static function syncPaymentDetail(PaymentDetail $paymentDetail, $isRemove = false)
    {
        $res = false;

        $billPay = BillPay::query()->where('payment_detail_id', $paymentDetail->id)->first();

        // 关联生成对应的付款记录
        if($isRemove)
        {
            $res = true;
            if(!empty($billPay))
            {

                $res = $billPay->delete();
            }
            return $res;
        }

        // 同步生成
        $billPay = new BillPay();

        $row = [
            'bill_period_id' => $paymentDetail->bill_period_id?:0,
            'supplier_id'    => $paymentDetail->supplier_id?:0,
            'payment_detail_id' => $paymentDetail->id?:0,
            'payment_schedule_id' =>$paymentDetail->payment_schedule_id?:0,
            'payment_type_id'   => $paymentDetail->payment_type_id?:0,
            'kind'             =>  $paymentDetail->pay_type?:'',
            'date' => $paymentDetail->time,
            'money' => $paymentDetail->money?:0,
            'company' => $paymentDetail->collecting_company?:'',
            'code' => $paymentDetail->code?:'',
            'memo' => $paymentDetail->memo?:'',
            'user_id' => $paymentDetail->user_id?:0,
        ];

        $billPay->fill($row);

        $res = $billPay->save();

        return $res;
    }
}
