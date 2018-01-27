<?php

namespace App\Http\Middleware;

use App\Models\BillPeriod;
use App\Models\UserEnv;
use Closure;

class UserEnvs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * 读取账期信息
         * 1. 若无账期信息,创建一条,并激活。
         */
        BillPeriod::initBillPeriod();

        /**
         * 读取并初始化用户的环境变量
         * 1. 若未读取到环境变量，则创建一个新的记录
         * 2. 若已存在环境变量，但初始化参数为空，则设置默认值
         */
        $env = UserEnv::initAuthEnv();

        return $next($request);
    }
}
