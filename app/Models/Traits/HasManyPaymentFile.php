<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午9:09
 */

namespace App\Models\Traits;


use App\Models\PaymentFile;

trait HasManyPaymentFile
{
    /**
     * 拥有 多个付款明细
     *
     * @return mixed
     */
    public function payment_files()
    {
        return $this->hasMany(PaymentFile::class);
    }

    /**
     * 计算文件数量
     * @param array $filter
     *
     * @return mixed
     */
    public function countFile($filter = [])
    {
        $query = $this->payment_files();

        if(isset($filter['is_import_success']))
        {
            $query->where('is_import_success', $filter['is_import_success']);
        }

        if(isset($filter['is_upload_success']))
        {
            $query->where('is_upload_success', $filter['is_upload_success']);
        }

        return $query->count();
    }


}