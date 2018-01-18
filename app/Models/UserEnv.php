<?php

namespace App\Models;

use Admin;
use App\Models\Traits\BelongsToAdministrator;
use Illuminate\Database\Eloquent\Model;

class UserEnv extends Model
{
    protected $table = 'admin_user_envs';

    protected $fillable = [
        'user_id',
        'name', 'env'
    ];

    /**
     * 默认账期 ID
     */
    const ENV_DEFAULT_BILL_PERIOD = 'default_bill_period_id';


    use BelongsToAdministrator;
    /**
     * 当前用户环境变量
     *
     * @param string $key
     *
     * @param string $default
     *
     * @return Model|mixed|null|string|static
     */
    public static function authEnv($key = '', $default = '')
    {
        $user = Admin::user();

        $default_env = [
            'user_id' => $user->id,
            "{$key}" => $default,
        ];

        $env = UserEnv::query()->firstOrCreate(['user_id'=>$user->id], ['name'=>$user->name, 'env'=>json_encode($default_env)]);

        if(!empty($key))
        {
            return isset($env->$key) ? $env->key : $default;
        }

        return $env;
    }
}
