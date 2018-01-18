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
        return empty($this->bill_period) ? '' : $this->bill_period->name;
    }

    /**
     * 账期月份
     * @return string
     */
    public function getBillPeriodMonthAttribute()
    {
        return empty($this->bill_period) ? '' : $this->bill_period->month;
    }

    /**
     * 账期备选
     * @param bool $onlyActive
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getBillPeriodOptions($onlyActive = true)
    {
        $query = BillPeriod::query();

        if($onlyActive)
        {
            $query->whereIn('status', ['active']);
        }

        return $query->get()->pluck('name', 'id');
    }


}