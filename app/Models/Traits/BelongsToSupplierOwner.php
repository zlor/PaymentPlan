<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午8:36
 */

namespace App\Models\Traits;


use App\Models\SupplierOwner;

trait BelongsToSupplierOwner
{

    /**
     * 供应商-所有人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(SupplierOwner::class, 'supplier_owner_id');
    }

    /**
     * 所有人-姓名
     * @return string
     */
    public function getOwnerNameAttribute()
    {
        return empty($this->owner) ? '' : $this->owner->name;
    }


    /**
     * 获取所有人选项
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getOwnerOptions()
    {
        return SupplierOwner::query()->get()->pluck('name', 'id');
    }
}