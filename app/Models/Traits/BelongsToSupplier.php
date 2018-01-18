<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午8:41
 */

namespace App\Models\Traits;


use App\Models\Supplier;

trait BelongsToSupplier
{
    /**
     * 供应商
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * 供应商名称
     * @return string
     */
    public function getSupplierName()
    {
        return empty($this->supplier) ? '' : $this->supplier->name;
    }

    /**
     * 供应商所有人姓名
     *
     * @return string
     */
    public function getSupplierOwnerName()
    {
        return empty($this->supplier) ? '' : $this->supplier->owner_name;
    }

    /**
     * 获取供应商备选
     * @return \Illuminate\Support\Collection
     */
    public static function getSupplierOptions()
    {
        return Supplier::query()->get()->pluck('name', 'id');

    }
}