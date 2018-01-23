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
        $this->belongsToMany(PaymentSchedule::class, 'payment_schedule_file');
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
