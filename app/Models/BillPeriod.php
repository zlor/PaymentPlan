<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentFile;
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

    protected $fillable = [
        'user_id',
        'name', 'month', 'time_begin', 'time_end',
        'status', 'is_actived', 'is_locked', 'is_close',
        'charge_man',
    ];

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
    use HasManyPaymentSchedule, HasManyPaymentDetail, HasManyPaymentFile;


    public function isStandby()
    {
        return in_array($this->original['status'], [self::STATUS_STANDYBY]);
    }

    /**
     * 是否激活中
     * @return bool
     */
    public function isActive()
    {
        return in_array($this->original['status'], [self::STATUS_ACTIVE]);
    }

    /**
     * @return bool
     */
    public function allowSetPool()
    {
        return in_array($this->original['status'], ['standby','active']);
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

    /**
     * 当前现金余额
     *
     * @return mixed
     */
    public function getCurrentCashBalanceAttribute()
    {
        return $this->cash_total - $this->cash_paid;
    }

    /**
     * 当前承兑余额
     * @return mixed
     */
    public function getCurrentAcceptanceBalanceAttribute()
    {
        return $this->acceptance_line - $this->acceptance_paid;
    }

    /**
     * 当前总应付
     * @return mixed
     */
    public function getCurrentDueMoneyAttribute()
    {
        return $this->payment_schedules()->where('status', PaymentSchedule::STATUS_PAY)->sum('due_money');
    }

    /**
     * 统计供应商数量
     *
     * @param int $paymentTypeId
     *
     * @return mixed
     */
    public function countSuppliers($paymentTypeId = 0)
    {
        $query = $this->payment_schedules();

        if(!empty($paymentTypeId))
        {
            $query->where('payment_type_id', $paymentTypeId);
        }

        return $query->distinct('supplier_id')->count('supplier_id');
    }


    /**
     * 计算付款计划数量
     *
     * @param $filter
     *
     * @return mixed
     */
    public function countSchedules($filter)
    {
        $query = $this->payment_schedules();

        if(!empty($filter['status']))
        {
            $query->whereIn('status', is_array($filter['status'])?$filter['status']:explode(',', $filter['status']));
        }

        if(!empty($filter['payment_type_id']))
        {
            $query->where('payment_type_id', $filter['payment_type_id']);
        }

        return $query->count();
    }

    /**
     * 统计并获取
     *
     * @param int $paymentTypeId
     *
     * @return mixed
     */
    public function sumDueMoney($paymentTypeId = 0)
    {
        $query = $this->payment_schedules();

        if(!empty($paymentTypeId))
        {
            $query->where('payment_type_id', $paymentTypeId);
        }

        return $query->sum('due_money');
    }

    /**
     * 当前支付的现金
     *
     * @param array $filter
     *
     * @return mixed
     */
    public function sumCashPaid($filter)
    {
        $query = $this->payment_schedules();

        if(!empty($filter['paymentTypeId']))
        {
            $query->where('payment_type_id', $filter['paymentTypeId']);
        }

        return $query->sum('cash_paid');
    }
    /**
     * 当前支付的承兑
     * @param array $filter
     *
     * @return mixed
     */
    public function sumAcceptancePaid($filter)
    {
        $query = $this->payment_schedules();

        if(!empty($filter['paymentTypeId']))
        {
            $query->where('payment_type_id', $filter['paymentTypeId']);
        }

        return $query->sum('acceptance_paid');
    }

    /**
     * 统计并获取
     *
     * @param int $paymentTypeId
     *
     * @return mixed
     */
    public function sumPaidMoney($paymentTypeId = 0)
    {
        $query = $this->payment_schedules();

        if(!empty($paymentTypeId))
        {
            $query->where('payment_type_id', $paymentTypeId);
        }

        return $query->sum('cash_paid')+ $query->sum('acceptance_paid');
    }

    /**
     * 获取允许锁定的付款计划
     *
     * @return mixed
     */
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


    public static function haveActive()
    {
        return self::query()->whereIn('status', [self::STATUS_ACTIVE])->count() > 0;
    }


    /**
     * 初始化系统账期
     */
    public static function initBillPeriod()
    {
        $query =  BillPeriod::query();

        $count = $query->whereIn('status', [self::STATUS_STANDYBY, self::STATUS_ACTIVE])
                       ->count();

        if(!$count>0)
        {
            // 创建一条最新的
            return  self::autoSetPeriod();
        }

        // $count = $query->whereIn('status', [self::STATUS_ACTIVE])
        //                ->count();
        // if(!$count>0)
        // {
        //     // 将最接近月的就绪账期设置为激活账期
        //     $lastStandbyBillPeriod = $query->whereIn('status', [self::STATUS_STANDYBY])
        //                                    ->orderBy('month', 'desc')
        //                                    ->first();
        // }

        return true;
    }

    /**
     * 自动创建账期
     */
    protected static function autoSetPeriod()
    {
        // 创建规则
        $factoryRule = [
            'name'  => date('Y年m月'),
            'month' => date('Y-m'),
            'time_begin' => date('Y-m-01'),
            'time_end' => date('Y-m-d', strtotime(date('Y-m-01') . " +1 month -1 day")),
            'user_id' => 0,
            'status' => BillPeriod::STATUS_STANDYBY
        ];

        $billPeriod = new BillPeriod();

        $billPeriod->fill($factoryRule);



        return BillPeriod::query()->create();
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
