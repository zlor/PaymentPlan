<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\HasManyBillPeriodFlow;
use App\Models\Traits\HasManyPaymentDetail;
use App\Models\Traits\HasManyPaymentFile;
use App\Models\Traits\HasManyPaymentSchedule;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Types\Integer;

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
    use HasManyPaymentSchedule, HasManyPaymentDetail, HasManyPaymentFile, HasManyBillPeriodFlow;


    /**
     * 是否为就绪状态
     *
     * @return bool
     */
    public function isStandby()
    {
        return in_array($this->original['status'], [self::STATUS_STANDYBY]);
    }

    /**
     * 是否激活中
     *
     * @return bool
     */
    public function isActive()
    {
        return in_array($this->original['status'], [self::STATUS_ACTIVE]);
    }

    /**
     * 是否允许设置 资金池
     * @return bool
     */
    public function allowSetPool()
    {
        return in_array($this->original['status'], ['standby', 'active']);
    }

    /**
     *是否允许设置 计划的初始化
     *
     * &1 计划数为0
     * @return bool
     */
    public function allowSetSchedule()
    {
        // &1 计划数不为0时，不可初始化
        $count  = $this->payment_schedules->count();
        if($count>0)
        {
            return false;
        }

        return true;
    }

    /**
     * 获取当前账期的月份数字
     *
     * @param  boolean $needReturnInvoiceMonthMap 是否需要返回按当前账期排列好的发票月份字段
     *
     * @return integer|array
     */
    public function getMonthNumber($needReturnInvoiceMonthMap = false)
    {
        $month = $this->original['month'];
        $number = intval(date('m', strtotime($month)));

        if(!$needReturnInvoiceMonthMap)
        {
            return $number;
        }

        $map = [];

        for($i = $number-1; $i>$number-7; $i--)
        {
            $key = 'invoice_m_' . ($i<1 ? (12 + $i): $i );

            $map[$key] = $i;
        }

        asort($map);

        $map =  array_keys($map);

        return $map;
    }

    /**
     * 获取当前月份关联的发票月份表头
     */
    public function getMonthHead()
    {
        $month = $this->original['month'];
        $number = intval(date('m', strtotime($month)));
        $year      = intval(date('Y', strtotime($month)));

        $map = [];

        for($i = $number-1; $i>$number-12; $i--)
        {
            $key = 'invoice_m_' . ($i<1 ? (12 + $i): $i );

            $map[$key] = $i;
        }

        asort($map);

        $heads = [];

        foreach ($map as $field => $month)
        {
            $key = '';
            if($month<=0)
            {
                $key =  ($year-1).'年'.abs(12 + $month).'月发票';
            }else{
                $key =  ($year).'年'.abs($month).'月发票';
            }
            $heads[$key] = $field;
        }

        return $heads;
    }

    /**
     * 从给定的文本中猜测出，应付款付款周期的月的值
     *
     * @param $txt
     * @param $monthTxt
     * @return  Integer
     */
    public function guestCycleMonth($txt = '', $monthTxt = '')
    {
        if(str_contains($monthTxt, ['月']))
        {
            $targetMonthNum = intval( substr(trim($monthTxt), 0, -1));

            if($targetMonthNum>0 && $targetMonthNum<13)
            {
                return $targetMonthNum;
            }
        }

        $month = $this->original['month'];

        $number = intval(date('m', strtotime($month)));

        //默认为全要计算
        $default =  1;

        // 什么都没解析到,使用 90天，即前三个月的因付款可以不用计算
        //$default =  3;
        // 从 $txt 中解析文本
        // 中文+数字 分词获取月份
        //TODO 增加 payment_cycle 模型,对所有周期的描述性文字进行归档识别

        return $number - $default;
    }

     public function guestSuggestDueMoney($data, $month)
     {
         $supplier_balance = isset($data['supplier_balance'])?$data['supplier_balance']:0;

         if($supplier_balance<=0)
         {
             return 0;
         }
             //  获得参与计算的 发票金额字段
         $map = $this->getMonthNumber(true);

         $sum = 0;
         $offsetSum = 0;
         // 循环合计金额字段
         $isUsed =  false;
         foreach ($map as $item)
         {
             if($isUsed)
             {
                 $offsetSum += isset($data[$item]) ? $data[$item] : 0;

             }else{

                 $sum += isset($data[$item]) ? $data[$item] : 0;
                 if( $item == "invoice_m_{$month}")
                 {
                     $isUsed = true;
                 }
             }
         }
         return $supplier_balance - $offsetSum;
     }


    /**
     * 同步现金池（从资金流中汇总）
     *
     * @param $flowType
     *
     * @return bool
     */
    public function syncFlowMoney($flowType = '')
    {
        $checkMap = ['pay', 'collect'];

        if(!empty($flowType))
        {
            $checkMap = [$flowType];
        }

        if( in_array('pay', $checkMap))
        {
            $this->cash_paid = -1 * $this->bill_period_flows()
                                    ->where('type', 'pay')
                                    ->whereIn('kind', ['cash', 'tele_transfer'])
                                    ->sum('money');

            $this->acceptance_paid = -1 * $this->bill_period_flows()
                                            ->where('type', 'pay')
                                            ->where('kind', 'acceptance')
                                            ->sum('money');
        }

        if(in_array('collect', $checkMap))
        {
            $this->cash_collected = $this->bill_period_flows()
                    ->where('type', 'collect')
                    ->where('kind', 'cash')
                    ->sum('money');

            $this->acceptance_paid = $this->bill_period_flows()
                    ->where('type', 'collect')
                    ->where('kind', 'acceptance')
                    ->sum('money');
        }

        return $this->save();
    }

    /**
     * 现金初期值 (现金余额[银行存款] + 银行贷款)
     *
     * @return mixed
     */
    public function getInitCashAttribute()
    {
        return $this->cash_balance + $this->loan_balance;
    }

    /**
     * 现金总额、库存现金 （ 现金余额 + 已收款额 + 银行贷款）[确认收款-额度]
     *
     * @return mixed
     */
    public function getCashTotalAttribute()
    {
        return $this->cash_balance + $this->loan_balance + $this->cash_collected;
    }

    /**
     * cash_paid
     */
    // $this->cash_paid

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
     * 额度初期值 (信用额度 + 确认到款额度)
     *
     * @return mixed
     */
    public function getInitQuotaAttribute()
    {
        return $this->acceptance_line + $this->invoice_balance;
    }

    /**
     * 已支付的额度
     *
     * @return mixed
     */
    public function getQuotaPaidAttribute()
    {
        return $this->acceptance_paid;
    }

    /**
     * 已收到的额度
     *
     * @return mixed
     */
    public function getQuotaCollectedAttribute()
    {
        return $this->acceptance_collected;
    }

    /**
     * 额度总额 (银行承兑 + 已收承兑)
     *
     * @return mixed
     */
    public function getQuotaTotalAttribute()
    {
        return $this->init_quota + $this->acceptance_collected;
    }



    /**
     * 期初金额 (期初现金 + 期初额度)
     *
     * @return mixed
     */
    public function getInitTotalAttribute()
    {
        return $this->init_cash + $this->init_quota;
    }

    /**
     * 已支付总额 （支出的现金 + 支出的承兑）
     *
     * @return mixed
     */
    public function getPaidTotalAttribute()
    {
        return $this->cash_paid + $this->acceptance_paid;
    }

    /**
     * 收款总额
     *
     * @return mixed
     */
    public function getCollectTotalAttribute()
    {
        return $this->cash_collected + $this->acceptance_collected;
    }

    /**
     * 账期余额
     *
     * @return mixed
     */
    public function getBalanceAttribute()
    {
        return $this->init_total - $this->paid_total + $this->collect_total;
    }

    /**
     * 当前额度余额
     * @return mixed
     */
    public function getCurrentQuotaBalanceAttribute()
    {
        return $this->quota_total - $this->quota_paid;
    }



    /**
     * 当前承兑余额
     * @return mixed
     */
    public function getCurrentAcceptanceBalanceAttribute()
    {
        return $this->acceptance_line - $this->acceptance_paid + $this->acceptance_collected;
    }

    /**
     * 现金池
     *
     * @return mixed
     */
    public function getCashPoolAttribute()
    {
        return $this->current_cash_balance;
    }

    /**
     * 承兑池
     *
     * @return mixed
     */
    public function getAcceptancePoolAttribute()
    {
        return $this->current_acceptance_balance;
    }

    /**
     * 资金池
     *
     * @return mixed
     */
    public function getPoolAttribute()
    {
        return $this->balance;
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
        $query = $this->bill_period_flows();

        $query->where('type', 'pay')
              ->where('kind', 'cash');

        if(!empty($filter['payment_type_id']))
        {
            $query->where('payment_type_id', $filter['payment_type_id']);
        }

        return -1 * $query->sum('money');
    }

    public function sumTeleTransferPaid($filter)
    {
        $query = $this->bill_period_flows();

        $query->where('type', 'pay')
            ->where('kind', 'tele_transfer');

        if(!empty($filter['payment_type_id']))
        {
            $query->where('payment_type_id', $filter['payment_type_id']);
        }

        return -1 * $query->sum('money');
    }

    /**
     * 当前支付的承兑
     * @param array $filter
     *
     * @return mixed
     */
    public function sumAcceptancePaid($filter)
    {
        $query = $this->bill_period_flows();

        $query->where('type','pay')
            ->where('kind', 'acceptance');

        if(!empty($filter['payment_type_id']))
        {
            $query->where('payment_type_id', $filter['payment_type_id']);
        }

        return -1 * $query->sum('money');
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
        $query = $this->bill_period_flows();

        $query->where('type','pay');

        if(!empty($paymentTypeId))
        {
            $query->where('payment_type_id', $paymentTypeId);
        }

        return -1 * $query->sum('money');
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
     * 识别到下一个有效年月的账期。
     * @param $time
     *
     * @return mixed
     */
    protected static function getVaildMonthTime($time)
    {
        $count = BillPeriod::query()->where('month', date('Y-m', $time))->count();

        if($count>0)
        {
            $time = self::getVaildMonthTime(strtotime(date('Y-m-01', $time) . " +1 month"));
        }

        return $time;
    }

    /**
     * 自动创建账期
     */
    protected static function autoSetPeriod()
    {
        $time = self::getVaildMonthTime(time());

        // 创建规则
        $factoryRule = [
            'name'  => date('Y年m月', $time),
            'month' => date('Y-m', $time),
            'time_begin' => date('Y-m-01', $time),
            'time_end' => date('Y-m-d', strtotime(date('Y-m-01', $time) . " +1 month -1 day")),
            'user_id' => 0,
            'status' => BillPeriod::STATUS_STANDYBY
        ];

        $billPeriod = new BillPeriod();

        $billPeriod->fill($factoryRule);

        return $billPeriod->save();
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

    /** 
     * 从指定的账期拷贝所有付款计划。
     *
     */
    public function copyScheduleForInit(BillPeriod $fromBillPeriod, $initDiff = 1)
    {
        $initFieldsMap = [
            'status' => PaymentSchedule::STATUS_INIT,
            'bill_period_id' => $this->id,
            'batch' => 1,
            'is_checked' => 0,
            'is_locked'  => 0,
            'suggest_due_money' =>0,
        ];
        $copyFieldsMap = [
           'bill_period_id', 'supplier_id', 'payment_type_id', 'payment_materiel_id',
        'name', 'supplier_name', 'supplier_balance', 'supplier_lpu_balance', 'materiel_name', 'pay_cycle', 'charge_man',
        'batch', 'suggest_due_money','pay_cycle_month',
        'status', 'memo',
        'invoice_m_1','invoice_m_2','invoice_m_3',
        'invoice_m_4','invoice_m_5','invoice_m_6',
        'invoice_m_7','invoice_m_8','invoice_m_9',
        'invoice_m_10','invoice_m_11','invoice_m_12',
        'is_checked', 'is_locked',
        ];

        $fromSchedules = \App\Models\PaymentSchedule::query()->where('bill_period_id', $fromBillPeriod->id)->get();

        $toSchedules = [];

        foreach ($fromSchedules as $item)
        {
            $newRow = [];
            $itemRow = $item->toArray();

            foreach ($copyFieldsMap as $key)
            {   
                // 截至月份：特殊处理_按照增加的账期月份来增加截至月份
                if('pay_cycle_month' == $key)
                {
                    $newRow[$key] = $itemRow[$key] + $initDiff;

                    $newRow[$key] = $newRow[$key]>12 ? ($newRow[$key] - 12): $newRow[$key];

                    continue;
                }

                if(isset($initFieldsMap[$key]))
                {
                    $newRow[$key] = $initFieldsMap[$key];
                }else{
                    $newRow[$key] = $itemRow[$key];
                }
            }

            // 查找或者-> 新建
            $current = PaymentSchedule::query()->firstOrCreate([
                'supplier_id'=> $newRow['supplier_id'],
                // 'name'       => $newRow['name'],
                'bill_period_id'=> $newRow['bill_period_id'],
                'payment_type_id'=> $newRow['payment_type_id'],
                'payment_materiel_id'=> $newRow['payment_materiel_id'],
            ], $newRow);

            $toSchedules[] = $current->toArray();
        }

        return $toSchedules;
    }



}
