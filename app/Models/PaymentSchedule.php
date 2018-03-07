<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use App\Models\Traits\HasManyBillPeriodFlow;
use App\Models\Traits\HasManyPaymentDetail;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;

class PaymentSchedule extends Model
{
    protected $table = 'payment_schedules';

    protected $fillable = [
        'bill_period_id', 'supplier_id', 'payment_type_id', 'payment_materiel_id',
        'name', 'supplier_name', 'supplier_balance', 'supplier_lpu_balance', 'materiel_name', 'pay_cycle', 'charge_man',
        'batch', 'suggest_due_money','pay_cycle_month',
        'status', 'memo',
        'plan_time', 'plan_due_money', 'plan_man',
        'audit_time', 'audit_due_money', 'audit_man',
        'final_time', 'final_due_money', 'final_man',
        'cash_paid', 'acceptance_paid',
        'invoice_m_1','invoice_m_2','invoice_m_3',
        'invoice_m_4','invoice_m_5','invoice_m_6',
        'invoice_m_7','invoice_m_8','invoice_m_9',
        'invoice_m_10','invoice_m_11','invoice_m_12',
        'is_checked', 'is_locked',
    ];

    protected $casts = [
        'plan_due_money'    => 'double',
        'audit_due_money'   => 'double',
        'final_due_money'   => 'double',
        'cash_paid'         => 'double',
        'acceptance_paid'   => 'double',
        'suggest_due_money' => 'double',
        'supplier_balance'  => 'double',
        'supplier_lpu_balance' => 'double'
    ];

    const STATUS_INIT = 'init';
    const STATUS_INIT_WEB = 'web_init';
    const STATUS_INIT_IMPORT = 'import_init';
    const STATUS_PLAN     = 'plan';
    const STATUS_CONFIRM     = 'check_init';
    const STATUS_CHECK     = 'check';
    const STATUS_CHECK_AUDIT= 'check_audit';
    const STATUS_CHECK_FINAL= 'check_final';
    const STATUS_PAY= 'paying';
    const STATUS_LOCK= 'lock';
    const STATUS_FROZE = 'froze';

    use CommonOptions;
    /**
     * 归属于 账期、用户、供应商、物料、类型
     */
    use BelongsToBillPeriod, BelongsToAdministrator, BelongsToSupplier, BelongsToPaymentMateriel, BelongsToPaymentType;

    /**
     * 拥有 付款计划明细、资金流水明细
     */
    use HasManyPaymentDetail, HasManyBillPeriodFlow;



    /**
     * 在 select中显示的字段
     * @return string
     */
    public function select_text()
    {
        return $this->bill_period_name .'_'. $this->payment_type_name .' ('.$this->supplier_name.')';
    }

    public function getTitleExtAttribute()
    {
        return "{$this->name},{$this->supplier_name},";
    }

    public function setPlanDueMoneyAttribute($value)
    {
        $this->attributes['plan_due_money'] = doubleval($value);

        return  $this;
    }

    /**
     * 已付金额
     * @return mixed
     */
    public function getPaidMoneyAttribute()
    {
        return $this->cash_paid + $this->acceptance_paid;
    }

    public function hasPlanInfo()
    {
        return !empty($this->plan_time) && !empty($this->plan_man);
    }

    public function hasAuditInfo()
    {
        return !empty($this->audit_time) && !empty($this->audit_man);
    }

    public function hasFinalInfo()
    {
        return !empty($this->final_time) && !empty($this->final_man);
    }

    public function hasLockInfo()
    {
        return !empty($this->due_money) && intval(100*$this->due_money) != 0;
    }

    public function hasPayInfo()
    {
        return !empty($this->payment_details) && $this->payment_details()->count()>0;
    }

    /**
     * 允许导入数据覆盖的计划
     * @return bool
     */
    public function allowImportOverwrite()
    {
        return in_array($this->original['status'], ['init', 'import_init', 'web_init']);
    }

    /**
     * 允许计划的编辑
     *
     * @return bool
     */
    public function allowPlanEdit()
    {
        return in_array($this->original['status'], ['init', 'import_init', 'web_init']);
    }

    /**
     * 允许计划的 审核编辑
     * @return bool
     */
    public function allowAuditEdit()
    {
        return in_array($this->original['status'], ['check', 'check_init', 'check_audit']) || $this->allowPlanEdit();
    }

    /**
     * 允许计划 付款
     */
    public function allowPay()
    {
        return in_array($this->original['status'], [self::STATUS_PAY]);
    }


    /**
     * 同步现金池, 从资金流中同步统计
     */
    public function syncFlowMoney()
    {
        $this->cash_paid = -1 * $this->bill_period_flows()
                ->where('type', 'pay')
                ->where('kind', 'cash')
                ->sum('money');

        $this->acceptance_paid = -1 * $this->bill_period_flows()
                ->where('type', 'pay')
                ->where('kind', 'acceptance')
                ->sum('money');

        return $this->save();
    }

    /**
     * 批量修改界面，更新属性值
     *
     * @param $field
     * @param $params
     *
     * @return array
     */
    public function updateByBatch($field, $params)
    {
        $money = $params['money'];
        $time  = date('Y-m-d h:i:s', time());
        $user  = Admin::user();
        $man  = $user->name;
        $memo  = "[快速修改{$money},{$time},{$user->id}:{$man}]";

        switch ($field)
        {
            case 'plan_due_money':
                $this->plan_due_money = $money;
                $this->plan_time = $time;
                $this->plan_man  = $man;
                $this->memo .="(计划调整:$memo)";
                break;
            case 'audit_due_money':
                $this->audit_due_money = $money;
                $this->audit_time = $time;
                $this->audit_man  = $man;
                $this->memo_audit .= "(一次核定调整:$memo)";
                break;
            case 'final_due_money':
                $this->final_due_money = $money;
                $this->final_time = $time;
                $this->final_man  = $man;
                $this->memo_final .= "(二次核定调整:$memo)";
                break;
            case 'due_money':
                $this->due_money = $money;
                $this->memo .= "(敲定应付款:$memo)";
                break;

            default:break;
        }

        $data = [
            'success' =>false,
            'msg' => '',
        ];

        try{

            $res = $this->save();

            if($res){
                $data['success'] = true;
            }else{
                $data['msg'] = '更新失败';
            }

        }catch (Exception $e)
        {
            $msg = $e->getMessage();

            $data['msg'] = $msg;
        }

        return $data;

    }

    /**
     * 获得映射方案选项
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getImportMappingOptions()
    {
        $list = [];

        try{
            $import_mapping = config('import_mapping');

            $import_mapping = json_decode($import_mapping, true);

            if(isset($import_mapping['options']) && is_array($import_mapping['options']))
            {
                $list = $import_mapping['options'];
            }

        }catch (Exception $e)
        {
            Log::error(" Config lose [import_mapping]:",$e->getMessage());
        }

        $options = [];

        foreach ($list as $item)
        {
            $options[$item['name']] = $item['value'];
        }

        return $options;
    }

    /**
     * 获得映射方案参数
     *
     * @param string $mappingKey
     *
     * @return array|mixed
     */
    public static function getImportMappingParams($mappingKey = '')
    {
        $list = [];

        try{
            $import_mapping = config('import_mapping');

            $import_mapping = json_decode($import_mapping, true);

            if(isset($import_mapping['mapping']) && is_array($import_mapping['mapping']))
            {
                $list = $import_mapping['mapping'];
            }



        }catch (Exception $e)
        {
            Log::error(" Config lose [import_mapping]:",$e->getMessage());
        }

        $params = [];

        foreach ($list as $item)
        {
            $params[$item['key']] = $item;
        }

        if(!empty($mappingKey))
        {
            return isset($params[$mappingKey]) ? $params[$mappingKey] : [];
        }

        return $params;
    }

    /**
     * 做成计划(依据缓存好的上传文件)
     *
     * @param array $source
     *
     */
    public static function makeByFile($source = [])
    {
        $schedule = PaymentSchedule::create($source);

    }

}
