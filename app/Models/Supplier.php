<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'name', 'code', 'logo',
        'contact', 'address', 'tel',
        'head', 'supplier_owner_id',
    ];

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
     * 获取所有人选项
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getOwnerOptions()
    {
        return SupplierOwner::query()->get()->pluck('name', 'id');
    }
}
