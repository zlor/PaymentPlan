<?php

namespace App\Models;

use App\Models\Traits\BelongsToBillPeriod;
use App\Models\Traits\BelongsToPaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'bill_period_id', 'payment_type_id', 'user_id',
        'name', 'ext', 'path', 'size', 'type',
        'is_upload_success', 'is_import_success',
        'memo',
    ];

    const TYPE_PAYMENT  = 'payment_schedule';
    const TYPE_SUPPLIER = 'supplier';

    use BelongsToBillPeriod, BelongsToPaymentType;


    /**
     * 获取文件的大小
     *
     * @return string
     */
    public function getSizeTxtAttribute()
    {
        $size = empty($this->size)?0:$this->size;
        if($size >= 1073741824) {
            $size = round($size / 1073741824 * 100) / 100 . ' gb';
        } elseif($size >= 1048576) {
            $size = round($size / 1048576 * 100) / 100 . ' mb';
        } elseif($size >= 1024) {
            $size = round($size / 1024 * 100) / 100 . ' kb';
        } else {
            $size = $size . ' bytes';
        }
        return $size;
    }

    /**
     * 删除指定文件
     *
     * @param $disk
     * @param $path
     *
     * @return bool
     */
    public function removeOrigin($disk, $path)
    {
        $disk = Storage::disk($disk);

        $res = false;
        if ($disk->exists($path))
        {
            $res = $disk->delete($path);
        }

        return $res;
    }


}
