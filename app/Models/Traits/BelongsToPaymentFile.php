<?php
namespace App\Models\Traits;

use App\Models\PaymentFile;
use Closure;

trait BelongsToPaymentFile
{
    /**
     * 付款文件
     *
     * @return mixed
     */
    public function payment_file()
    {
        return $this->belongsTo(PaymentFile::class, 'file_id');
    }

    /**
     * 付款文件-名称
     * @return string
     */
    public function getPaymentFileNameAttribute()
    {
        return empty($this->payment_file) ? '' : $this->payment_file->name;
    }

    /**
     * 获取付款文件-备选
     *
     * @param $callable Closure 回调
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentFileOptions(Closure $callable = null)
    {
        $query = PaymentFile::query();

        if (! is_null($callable))
        {
            return call_user_func($callable, $query);
        }

        return $query->get()->pluck('name', 'id');
    }
}