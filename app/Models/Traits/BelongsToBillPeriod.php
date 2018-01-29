<?php
namespace App\Models\Traits;


use App\Models\BillPeriod;

trait BelongsToBillPeriod
{

    /**
     * 账期
     * @return mixed
     */
    public function bill_period()
    {
        return $this->belongsTo(BillPeriod::class, 'bill_period_id');
    }

    /**
     * 账期代称
     * @return string
     */
    public function getBillPeriodNameAttribute()
    {
        $bill_period = $this->bill_period()->first();
        return empty($bill_period) ? '' : $bill_period->name;
    }

    /**
     * 账期月份
     * @return string
     */
    public function getBillPeriodMonthAttribute()
    {
        $bill_period = $this->bill_period()->first();
        return empty($bill_period) ? '' : $bill_period->month;
    }

    /**
     * 账期备选
     * @param bool $noMore
     * @param bool $allowStatus
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getBillPeriodOptions($noMore = true, $allowStatus = [])
    {
        $query = BillPeriod::query();

        $defaultStatus = ['active'];

        if($noMore)
        {
            $allowStatus = empty($allowStatus) ? $defaultStatus : $allowStatus;
        }else{
            $allowStatus = array_merge($defaultStatus, $allowStatus);
        }

        $query->whereIn('status', $allowStatus);

        return $query->get()->pluck('name', 'id');
    }


}