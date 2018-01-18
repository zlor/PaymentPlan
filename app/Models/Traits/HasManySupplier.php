<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午9:18
 */

namespace App\Models\Traits;


use App\Models\Supplier;

trait HasManySupplier
{
    /**
     * 供应商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}