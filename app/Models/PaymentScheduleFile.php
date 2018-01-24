<?php

namespace App\Models;

use App\Models\Traits\BelongsToAdministrator;
use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentFile;
use App\Models\Traits\BelongsToPaymentMateriel;
use App\Models\Traits\BelongsToPaymentSchedule;
use App\Models\Traits\BelongsToPaymentType;
use App\Models\Traits\BelongsToSupplier;
use App\Models\Traits\CommonOptions;
use App\Models\Traits\HasManyPaymentDetail;
use Illuminate\Database\Eloquent\Model;

class PaymentScheduleFile extends Model
{
    protected $table = 'payment_schedule_files';

    protected $fillable = [
        'file_id', 'payment_schedule_id', 'user_id',
        'number',
        'is_success', 'is_overwrite',
        'import_msg',
        'import_source',
    ];

    use CommonOptions;
    /**
     * 归属于 账期、用户、供应商、物料、类型
     */
    use BelongsToAdministrator, BelongsToPaymentSchedule, BelongsToPaymentFile;


    public function setImportSourceAttribute($value)
    {
        $this->attributes['import_source'] = json_encode($value);
        return $this;
    }

    public function getImportSourceAttribute()
    {
        return json_decode($this->attributes['import_source'], true);
    }

    public function setImportMsgAttribute($value)
    {
        $this->attributes['import_msg'] = json_encode($value);

        return $this;
    }

    public function getImportMsgAttribute()
    {
        return json_decode($this->attributes['import_msg'], true);
    }




}
