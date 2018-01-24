<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use App\Models\Traits\HasManyPaymentDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;

class PaymentSchedule extends Model
{
    protected $table = 'payment_schedules';

    protected $fillable = [
        'bill_period_id', 'supplier_id', 'payment_type_id', 'payment_materiel_id',
        'name', 'supplier_name', 'supplier_balance', 'supplier_lpu_balance', 'materiel_name', 'pay_cycle', 'charge_man',
        'batch', 'suggest_due_money',
        'status', 'memo',
        'plan_time', 'plan_due_money', 'plan_man',
        'audit_time', 'audit_due_money', 'audit_man',
        'final_time', 'final_due_money', 'final_man',
        'cash_paid', 'acceptance_paid',
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

    use CommonOptions;
    /**
     * 归属于 账期、用户、供应商、物料、类型
     */
    use BelongsToBillPeriod, BelongsToAdministrator, BelongsToSupplier, BelongsToPaymentMateriel, BelongsToPaymentType;

    /**
     * 拥有 付款明细
     */
    use HasManyPaymentDetail;



    /**
     * 在 select中显示的字段
     * @return string
     */
    public function select_text()
    {
        return $this->bill_period_name .'_'. $this->payment_type_name .' ('.$this->supplier_name.')';
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

    /**
     * 允许导入数据覆盖的计划
     * @return bool
     */
    public function allowImportOverwrite()
    {
        return in_array($this->original['status'], ['init', 'import_init', 'web_init']);
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

            $list = $import_mapping['options'];
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

            $list = $import_mapping['mapping'];

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
