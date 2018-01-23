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

    use CommonOptions;
    /**
     * 归属于 账期、用户、供应商、物料、类型
     */
    use BelongsToAdministrator, BelongsToPaymentSchedule, BelongsToPaymentFile;


}
