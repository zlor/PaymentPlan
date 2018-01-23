<?php

namespace App\Models;

use Admin;
use App\Models\Traits\BelongsToAdministrator;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserEnv extends Model
{
    protected $table = 'admin_user_envs';

    protected $fillable = [
        'user_id',
        'name', 'env'
    ];

    /**
     * 首选 账期_ID
     */
    const ENV_DEFAULT_BILL_PERIOD = 'default_bill_period_id';

    /**
     * 首选 菜单样式
     */
    const ENV_DEFAULT_SLIDER_CLASS = 'default_slider_class';

    /**
     * 归属 用户
     */
    use BelongsToAdministrator;

    /**
     * 设置 env 参数值
     */
    public function setEnvAttribute($options)
    {
        $originOptions = isset($this->original['env'])?json_decode($this->original['env'], true):[];

        //遍历属性，更新原有属性 或者 增加新的属性
        foreach ($options as $key => $value)
        {
            $originOptions[$key] = $value;
        }

        $this->attributes['env'] = json_encode($originOptions);

        return $this;
    }


    /**
     * 初始化当前用户的环境参数
     *
     * 初始化来源「Middleware:UserEnvs.php」 as 「user.envs」,成功登录后初始化数据
     *
     * @param array $options
     *
     * @return Model
     */
    public static function initAuthEnv($options = [])
    {
        $user = Admin::user();

        if(empty($options))
        {
            // 初始化参数值
            $options = [
                UserEnv::ENV_DEFAULT_BILL_PERIOD => 0,
                UserEnv::ENV_DEFAULT_SLIDER_CLASS => '',
            ];
        }

        // 若不存在，则以 $options 作为参数初始化
        $userEnv = UserEnv::query()->firstOrCreate(['user_id'=>$user->id], [
            'name' => $user->name,
            'env'  => $options,
        ]);

        $envOptions = json_decode($userEnv->env, true);

        // 例外状况, 默认账期已不再允许范围内
        if(!BillPeriod::allowDefaultPeriod($envOptions[self::ENV_DEFAULT_BILL_PERIOD]))
        {
            $env[self::ENV_DEFAULT_BILL_PERIOD] = BillPeriod::getCurrentId();

            $userEnv->env = $env;

            $userEnv->save();
        }

        // 返回实例
        return $userEnv;
    }

    /**
     * 更新当前用户的环境参数
     *
     * @param array $options
     *
     * @return bool
     */
    public static function updateAuthEnv($options = [])
    {
        $env = UserEnv::authEnv();

        $env->env = $options;

        return $env->save();
    }

    /**
     * 获取当前用户的 环境模型
     *
     * @return Model|null|static
     */
    public static function authEnv()
    {
        $user = Admin::user();

        $userEnv = UserEnv::query()->where('user_id', $user->id)->first();

        // 若不存在，则初始化
        $userEnv = empty($userEnv) ? (UserEnv::initAuthEnv()) : $userEnv;

        return $userEnv;
    }


    /**
     * 查询当前用户环境变量
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed|string
     */
    public static function getEnv($key = '', $default = '')
    {
        $userEnv = self::authEnv();

        $env = json_decode($userEnv->env, true);

        if($key != '' )
        {
            return isset($env[$key]) ? $env[$key] : $default;
        }

        return $env;
    }

    /**
     * 获取当前的账期对象
     *
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public static function getCurrentPeriod()
    {
        return BillPeriod::query()->findOrFail(self::getEnv(self::ENV_DEFAULT_BILL_PERIOD, 0));
    }
}
