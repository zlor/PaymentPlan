<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2018/1/18
 * Time: 上午8:36
 */

namespace App\Models\Traits;

use Encore\Admin\Auth\Database\Administrator;

trait BelongsToAdministrator
{
    /**
     * 操作人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }

    /**
     * 操作人姓名
     * @return string
     */
    public function getUserNameAttribute()
    {
        return empty($this->user) ? '' : $this->user->name;
    }

    /**
     * 操作人账户名
     * @return string
     */
    public function getUserAccountAttribute()
    {
        return empty($this->user) ? '' : $this->user->username;
    }

    /**
     * 获取用户备选
     * @return \Illuminate\Support\Collection
     */
    public static function getUserOptions()
    {
        return Administrator::query()->get()->pluck('name', 'id');
    }
}