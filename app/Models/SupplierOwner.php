<?php

namespace App\Models;

use App\Models\Traits\HasManySupplier;
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
     * 拥有 供应商
     */
    use HasManySupplier;
}
