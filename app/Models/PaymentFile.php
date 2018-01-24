<?php

namespace App\Models;

use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\HasManyPaymentSchedule;
use App\Models\Traits\UploadFileTool;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PaymentFile extends File
{
    /**
     * 上传文件
     */
    use UploadFileTool;

    /**
     * 关联的付款计划
     */
    public function payment_schedule()
    {
        return $this->belongsToMany(PaymentSchedule::class, 'payment_schedule_file');
    }

    /**
     * 预载入数据
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment_schedule_files()
    {
        return $this->hasMany(PaymentScheduleFile::class, 'file_id');
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.import.directory.'.$this->type);
    }

    /**
     * 模型的「启动」方法
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('payment', function(Builder $builder) {
            $builder->where('type', self::TYPE_PAYMENT);
        });
    }

    /**
     * 获取文件的状态
     */
    public function getStatusExtAttribute()
    {
        if($this->is_import_success)
        {
            return '数据已载入';

        }elseif($this->is_upload_success){

            return '上传成功';

        }else{

            return '上传不完整';
        }
    }

    public function getImportMsgAttribute()
    {
        return json_decode($this->attributes['import_msg'], true);
    }

    public function setImportMsgAttribute($value)
    {
        $this->attributes['import_msg'] = json_encode($value);

        return $this;
    }

    /**
     * 将文件数据缓存到 payment_schedule_files
     */
    public function cacheFile($options)
    {
        $file = $this;
        Excel::load($file->getLocalPath(), function ($reader) use (& $data) {
            $reader = $reader->getSheet(0);
            $data = $reader->toArray();
        });

        // 默认预设
        $columnMap = [
            'name' => 1,
            'supplier_name' =>2,
            'materiel_name' =>3,
            'charge_man' => 4,
            'pay_cycle'  => 5,
            'supplier_balance' =>6,
            'supplier_lpy_balance' =>7,
            // 计划付款
            'plan_due_money' => 8,
            // 下月付款
            'plan_next_month_money'=>9,
        ];
        $fromRowNumber = 5;
        $fromColumnNumber = 1;

        // 加载外部预设配置
        if(isset($options['heads']))
        {
            $columnMap = $options['heads'];
        }
        if(isset($options['skip_row_number']))
        {
            $fromRowNumber = intval($options['skip_row_number']);
        }
        if(isset($options['skip_column_number']))
        {
            $fromColumnNumber = intval($options['skip_column_number']);
        }

        $schedule_files = [];

        $user =  Admin::user();

        // 预加载到 payment_schedule_file 中
        for($rowIndex = $fromRowNumber; $rowIndex< count($data); $rowIndex++)
        {

            $import_source = [];

            $import_source['all'] = $data[$rowIndex];

            $import_source['filter']['plan_time'] = date('Y-m-d h:i:s');
            $import_source['filter']['plan_man'] = $user->name;

            foreach ($columnMap as $key => $value)
            {
                $import_source['filter'][$key] = isset($data[$rowIndex][$value]) ? $data[$rowIndex][$value] : '';
            }

            $schedule_files[] = [
                'user_id'       => $user->id,
                'number'        => $rowIndex,
                'is_success'    => false,
                'is_overwrite'  => false,
                'import_source' => $import_source,
            ];
        }

        return $file->payment_schedule_files()->createMany($schedule_files);
    }

    /**
     * 生成付款计划
     *
     * @param  $allowOverwrite boolean  相同的计划，是否允许覆盖
     *
     * @param  $useLastWhenRepeat boolean  当Excel中出现相同的数据时，是否要使用后识别的覆盖前者
     *
     * @return array
     */
    public function setupSchedule($allowOverwrite = false, $useLastWhenRepeat = false)
    {
        // 从预设的文件中开始生成计划
        // 账期
        $bill_period = $this->bill_period;
        // 类型
        $payment_type = $this->payment_type;

        // 缓存数据
        $mapping = [];

        $rule = [];

        // 读取所有不可更改的计划，验证重复性
        $schedules = $bill_period->payment_schedules()->get();
        foreach ($schedules as $schedule)
        {
            $uniqueKey = join('_', [
                $schedule['supplier_id'],
                $schedule['bill_period_id'],
                $schedule['payment_type_id'],
                $schedule['payment_materiel_id']
            ]);
            $rule['repeat'][$uniqueKey] = [
                'type' => $schedule->allowImportOverwrite()?'db_init':'db_lock',
                'title' => "既存计划({$schedule->supplier_name}-{$schedule->materiel_name},科目:{$schedule->name},[ID:{$schedule->id}，批次:{$schedule->batch}])",
            ];
        }

        // 缓存文件
        $caches = $this->payment_schedule_files()->get();

        // 导入信息统计
        $result = [
            'total'     => 0,
            'success'   => 0,
            'fail'      => 0,
            'fail_repeat'=> 0,
            'success_new' => 0,
            'success_overwrite' => 0,
            'msg' => []
        ];

        foreach ($caches as $cache)
        {
            $import_source = $cache->import_source;

            $row = $import_source['filter'];
            $msg_error   = [];
            $msg_warning = [];
            $msg_info    = [];

            // 创建新的计划
            $newRow = $row;

            // 设置格式
            $newRow['supplier_balance']      = empty($newRow['supplier_balance'])? 0 : doubleval(str_replace(',', '', $newRow['supplier_balance']));
            $newRow['supplier_lpu_balance']  = empty($newRow['supplier_lpu_balance'])? 0 : doubleval(str_replace(',', '', $newRow['supplier_lpu_balance']));
            $newRow['plan_next_month_money'] = empty($newRow['plan_next_month_money'])? 0 : doubleval(str_replace(',', '', $newRow['plan_next_month_money']));
            $newRow['plan_due_money']        = empty($newRow['plan_due_money'])? 0 : doubleval(str_replace(',', '', $newRow['plan_due_money']));

            // 设置账期
            $newRow['bill_period_id']  = $bill_period->id;
            // 设置类型
            $newRow['payment_type_id'] = $payment_type->id;
            // 设置状态
            $newRow['status'] =  'import_init';
            // 文件批次
            $newRow['batch']  = $this->id;

            // 识别供应商
            if( ! isset($mapping['supplier'][$row['supplier_name']]))
            {
                $mapping['supplier'][$row['supplier_name']] = Supplier::guestOrCreate(['name'=>$row['supplier_name']]);
            }
            $supplier = $mapping['supplier'][$row['supplier_name']];

            $newRow['supplier_id'] = $supplier->id;

            // 识别物料
            if( !isset($mapping['materiel'][$row['materiel_name']]))
            {
                $mapping['materiel'][$row['materiel_name']] = PaymentMateriel::guestOrCreate(['name'=>$row['materiel_name']]);
            }
            $materiel = $mapping['materiel'][$row['materiel_name']];

            $newRow['payment_materiel_id'] = $materiel->id;

            /**
             * 验证数据有效性
             */
            $uniqueKey = join('_', [
                $newRow['supplier_id'],
                $newRow['bill_period_id'],
                $newRow['payment_type_id'],
                $newRow['payment_materiel_id']
            ]);

            // 验证重复性
            if(!isset($rule['repeat'][$uniqueKey]))
            {
                $current = PaymentSchedule::query()->create($newRow);

                $msg_info[] = "Info【{$cache->payment_file_name},行{$cache->number}】，新建计划为({$current->supplier_name}-{$current->materiel_name},科目:{$current->name},[ID:{$current->id}])";

                // 账期ID+类型ID+供应商ID+物料ID
                $rule['repeat'][$uniqueKey] = [
                    'type' => 'cache_init',
                    'title' => "【{$cache->payment_file_name},行{$cache->number}】，新建计划为({$current->supplier_name}-{$current->materiel_name},科目:{$current->name},[ID:{$current->id}])",
                ];

                $result['success_new']++;

            }
            else if($rule['repeat'][$uniqueKey]['type'] == 'cache_init')
            {
                // 本次导入的未锁定计划
                // 允许覆盖
                if($useLastWhenRepeat)
                {
                    // 查找或者-> 新建
                    $current = PaymentSchedule::query()->firstOrCreate([
                        'supplier_id'=> $newRow['supplier_id'],
                        // 'name'       => $newRow['name'],
                        'bill_period_id'=> $newRow['bill_period_id'],
                        'payment_type_id'=> $newRow['payment_type_id'],
                        'payment_materiel_id'=> $newRow['payment_materiel_id'],
                    ], $newRow);

                    $cache->is_overwrite =  true;

                    $result['success_overwrite']++;

                    $msg_warning[] = "Warn【{$cache->payment_file_name},行{$cache->number}】，重复并覆盖{ ".$rule['repeat'][$uniqueKey]['title'].'}';
                }else{
                    $msg_error[] = "Error【{$cache->payment_file_name},行{$cache->number}】，重复{ ".$rule['repeat'][$uniqueKey]['title'].'}';
                }
            }
            else if($rule['repeat'][$uniqueKey]['type'] == 'db_init')
            {
                // 存在，但未被锁定
                // 允许覆盖数据表中的「未审核的计划」
                if($allowOverwrite)
                {
                    // 更新或者新建
                    $current = PaymentSchedule::query()->updateOrCreate([
                        'supplier_id'=> $newRow['supplier_id'],
                        'bill_period_id'=> $newRow['bill_period_id'],
                        'payment_type_id'=> $newRow['payment_type_id'],
                        'payment_materiel_id'=> $newRow['payment_materiel_id'],
                    ], $newRow);

                    $cache->is_overwrite =  true;

                    $result['success_overwrite']++;

                    $msg_warning[] = "Warn【{$cache->payment_file_name},行{$cache->number}】，重复并覆写{".$rule['repeat'][$uniqueKey]['title'].'}';

                }else{
                    $msg_error[] = "Error【{$cache->payment_file_name},行{$cache->number}】，重复{该计划已开始审核，".$rule['repeat'][$uniqueKey]['title'].'}';
                }

            }else if($rule['repeat'][$uniqueKey]['type'] == 'db_lock')
            {
                // 存在，且已经锁定
                // 已开始审核的计划,不可被导入覆盖
                $msg_error[] = "Error【{$cache->payment_file_name},行{$cache->number}】，重复(该计划已开始审核，".$rule['repeat'][$uniqueKey]['title'].')';
            }

            // TODO 暂无规则 (可交由web端修正的内容)
            // 数据丢失，未填写计划金额


            if(count($msg_error) == 0 && isset($current) && !empty($current->id))
            {
                // 设置关联
                $cache->payment_schedule()->associate($current);

                $cache->is_success = true;

                $result['success']++;

            }else{
                $cache->is_success = false;

                $result['fail']++;
            }

            $result['total']++;

            $result['msg'] = array_merge($result['msg'], $msg_info, $msg_warning, $msg_error);

            // 存入导入信息记录
            $cache->import_msg = ['info'=>$msg_info, 'error'=>$msg_error, 'warning'=>$msg_warning];

            $res = $cache->save();
        }

        if($result['success'] >0)
        {
            $this->is_import_success = true;
        }

        $this->import_msg = $result['msg'];

        $this->save();

        // 统计导入信息
        return $result;
    }

    /**
     * 导入时需要检验数据唯一性的关键字
     *
     * @return string
     */
    public function getImportUniqueKey()
    {
        return $this->attributes['bill_period_id'].'_'.$this->attributes['payment_type_id'].'_'.$this->attributes['payment_materiel_id'].'_'.$this->attributes['supplier_id'];
    }

    public function buildWithSource($source = [])
    {
        // 获取供应商数据

        // 获取物料数据

        // 获取类型数据

        $this->fill($source);

        return $this;
    }


    /**
     * 是否载入成功
     * @return mixed
     */
    public function importSuccess()
    {
        return $this->is_import_success;
    }



    /**
     * 删除当前文件
     *
     * @param bool $needRemoveOriginFile
     *
     * @return bool|null
     */
    public function remove($needRemoveOriginFile = true)
    {
        if($needRemoveOriginFile)
        {
            $this->removeOrigin('import', substr($this->path, 1));
        }

        return $this->delete();
    }

    public function getLocalPath()
    {
        return Storage::disk('import')->path(substr($this->path, 1));
    }


    /**
     * 由上传的文件构造 付款计划专用的文件
     *
     * @param BillPeriod $billPeriod
     * @param string        $name
     * @param UploadedFile $file
     *
     * @return PaymentFile
     */
    public static function makeFile(BillPeriod $billPeriod, $name = '', UploadedFile $file)
    {
        $user = Admin::user();

        $paymentFile  = new PaymentFile([
            'ext'     => $file->extension(),
            'size'    => $file->getSize(),
            'user_id' => $user->id,
            'type'    => self::TYPE_PAYMENT,
        ]);
        // 指定盘符
        $paymentFile->disk('import');

        // 指定文件名
        if(!empty($name))
        {
            $paymentFile->rename();

            $paymentFile->name($name.'.'.$file->extension());

        }else{

            $paymentFile->name($file->getClientOriginalName());
        }

        // 上传文件
        $paymentFile->path = $paymentFile->upload($file);

        $paymentFile->is_upload_success = true;

        // 获取实际文件名
        $paymentFile->name = $paymentFile->getFileName();


        return $paymentFile;
    }

}
