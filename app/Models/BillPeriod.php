<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentSchedule;
use Illuminate\Database\Eloquent\Model;

class BillPeriod extends Model
{
    protected $table = 'bill_periods';

    /**
     * 归属于 用户
     */
    use BelongsToAdministrator;

    /**
     * 拥有 付款计划、付款明细
     */
    use HasManyPaymentSchedule, HasManyPaymentDetail;

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
     * 状态备选
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return trans_options('status', ['standby', 'active', 'lock', 'close'], 'bill.period');
    }

    /**
     * 当前用户操作中的 账期
     *
     * 若未设定账期，默认获取当前最新active账期的ID
     *
     * @return Model|mixed|null|string|static
     */
    public static function getCurrentId()
    {
        $bill_period = self::query()
                            ->whereIn('status', ['active'])
                            ->orderBy('id', 'desc')
                            ->first();
        // 返回默认账期的ID
        return UserEnv::authEnv(
            UserEnv::ENV_DEFAULT_BILL_PERIOD,
            empty($bill_period) ? 0 : $bill_period->id
        );
    }
}
