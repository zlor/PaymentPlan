<?php
/**
 * Created by PhpStorm.
 * User: juli
 * Date: 2017/09/30
 * Time: 下午1:47
 */

namespace App\Http\Controllers\Traits;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

trait GetURLFromRouteName
{
    protected $routeMap = [];

    public function getUrl($key, $params = [], $method = ''){
        try{
            if(in_array($method, ['PUT', 'put']))
            {
                $params['_method'] = strtoupper($method);
                $params['_token'] = csrf_token();
            }

            $url = URL::route($this->routeMap[$key], is_array($params)?$params:[]);
        }catch (\Exception $e){
            Log::error("路由中未找到名为[{$key}]的匹配项".json_encode($e));
            $url = '';
        }
        return $url;
    }
}