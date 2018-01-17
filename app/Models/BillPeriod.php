<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillPeriod extends Model
{
    protected $table = 'bill_periods';


    /**
     * 现金总额 （ 现金余额 + 确认计划收款额 + 预计收款）
     *
     * @return mixed
     */
    public function getCashTotalAttribute()
    {
        return $this->cash_balance + $this->invoice_balance + $this->except_balance;
    }

    /**
     * 已支付总额 （支出的现金 + 支出的承兑）
     * @return mixed
     */
    public function getPaidTotalAttritbute()
    {
        return $this->cash_paid + $this->acceptance_paid;
    }


    /**
     * 现金池
     *
     * @return mixed
     */
    public function getCashPoolAttribute()
    {
        return $this->cash_total + $this->acceptance_line;
    }

    /**
     * 账期余额
     *
     * @return mixed
     */
    public function getBalanceAttribute()
    {
        return $this->cash_pool - $this->paid_total;
    }

    /**
     *
     */
    public static function getStatusOptions()
    {
        return trans_options('status', ['standby', 'active', 'lock', 'close'], 'bill.period');
    }
}
