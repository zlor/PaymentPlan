<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOwner extends Model
{
    protected $table = 'supplier_owners';

    protected $fillable = [
        'name', 'code', 'company',
        'tel', 'memo',
    ];

    /**
     * 供应商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliers()
    {
       return $this->hasMany(Supplier::class, 'supplier_owner_id');
    }
}
