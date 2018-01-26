<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentSchedule;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class BillPeriod
 *
 * 账期 [账期数据，第一条数据初始化产生，其后的数据自动生成。]
 *
 * [就绪: 激活前的准备状态]
 * 可以向就绪状态的账期内，先行导入数据
 *
 * [激活: 当前付款计划/付款明细基于的账期状态]
 * 可以向激活状态下的账期，导入数据，调整并审核付账计划，录入付账明细
 *
 * 激活状态的账期始终保持在一个；
 *
 * 检测到不存在其他「激活」中的账期，方可激活当前账期；
 *
 * 在激活当前账期时，自动生成下一个就绪状态的账期；
 *
 * [锁定]
 * 锁定状态，在出现新的激活账期后即不可逆。
 * 锁定状态的账期，只能由财务主管来调整数据，每次调整数据，会记录调整履历；
 *
 * [关闭]
 * 关闭状态，快照指定的锁定状态账期。关闭状态，将封闭所有调整入口
 *
 *
 * @package App\Models
 */
class BillPeriod extends Model
{
    protected $table = 'bill_periods';

    const STATUS_STANDYBY = 'standby';
    const STATUS_ACTIVE = 'active';
    const STATUS_LOCK = 'lock';
    const STATUS_CLOSE = 'close';

    /**
     * 归属于 用户
     */
    use BelongsToAdministrator;

    /**
     * 拥有 付款计划、付款明细
     */
    use HasManyPaymentSchedule, HasManyPaymentDetail;


    /**
     * 是否激活中
     * @return bool
     */
    public function isActive()
    {
        return in_array($this->original['status'], ['active']);
    }

    /**
     * 同步现金池
     */
    public function syncMoney()
    {
        $this->cash_paid = $this->payment_details()->where('pay_type', 'cash')->sum('money');

        $this->acceptance_paid = $this->payment_details()->where('pay_type', 'acceptance')->sum('money');

        return $this->save();
    }

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
    public function getPaidTotalAttribute()
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

    public function getCurrentCashBalanceAttribute()
    {
        return $this->cash_total - $this->cash_paid;
    }

    public function getCurrentAcceptanceBalanceAttribute()
    {
        return $this->acceptance_line - $this->acceptance_paid;
    }

    public function getLockSchedules()
    {
        return $this->payment_schedules()->whereNotIn('status', ['init', 'web_init', 'import_init'])->get();
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
     * @return integer
     */
    public static function getCurrentId()
    {
        $bill_period = self::getDefault();

        // 返回默认账期的ID
        return empty($bill_period) ? 0 : $bill_period->id;
    }

    /**
     * 获得默认账期
     *
     * 激活中的账期只能存在一个
     *
     * @return BillPeriod|Model|null|static
     */
    public static function getDefault()
    {
        $bill_period = self::query()
            ->whereIn('status', ['active'])
            ->orderBy('id', 'desc')
            ->first();

        return empty($bill_period) ? (new BillPeriod()) : $bill_period;
    }

    /**
     * 是否为允许的默认账期
     *
     * 激活状态 ( // 就绪状态 )
     *
     * @param int $periodId
     *
     * @return bool
     */
    public static function allowDefaultPeriod($periodId = 0)
    {
        return self::query()
                ->where('id', $periodId)
                ->whereIn('status', ['active'])
                ->count() > 0;
    }


    /**
     * 获取环境变量中的 当前账期
     */
    public static function envCurrent()
    {
        $billPeriodId = UserEnv::getEnv(UserEnv::ENV_DEFAULT_BILL_PERIOD);

        $billPeriod = self::query()->find($billPeriodId);

        return empty($billPeriod)? self::getDefault(): $billPeriod;
    }


    /**
     * 导入付款计划
     *
     * @param PaymentFile $file
     */
    public static function importSchedule(PaymentFile $file, $options = [])
    {


        // 由 payment_schedule_file 向 payment_schedule 作成计划,并保留反馈信息
        // supplier_name:required,unique; materiel_name:required; charge_man:required;
        $file->loadFiles();




    }


}
