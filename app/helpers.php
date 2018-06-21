<?php

if(! function_exists('withLayUI_Table')){
    /**
     * 使用LayUI
     *
     * @param Encore\Admin\Grid\ $grid
     *
     * @return Encore\Admin\Grid\ $grid
     */
    function withLayUI_Table($grid){
        Admin::css([admin_asset("/vendor/layui/css/layui.css")]);
        Admin::js([
            admin_asset("/vendor/layui/layui.js"),
            admin_asset("/vendor/layui/lay/modules/table.js"),
        ]);
        Admin::script(view("layouts.layui_table_init")->render());

        $grid->with(["useLayUI"=>"yes"]);

        return $grid;
    }
}


if(! function_exists('_A')){
    /**
     * 获得列表页面编辑用的a标签
     *
     * @param string $text
     * @param array  $tagA
     * @param array  $data
     *
     * @return string
     */
    function _A($text='',$tagA = [], $data = []){
        $tagA['text'] = $text;
        return _editHtml('A', $tagA, $data);
    }
}


if(! function_exists('_editHtml')){
    /**
     * @param string $type
     * @param array  $tagA
     * @param array  $data
     *
     * @return string
     */
    function _editHtml($type = 'action', $tagA = [], $data = []){
        if('action' ==  $type || 'A' == $type) {

            $href = isset($tagA['href'])?$tagA['href']:'javascript:;';

            // 标签样式
            if(!isset($tagA['class'])){

                $class = '';

            }else if( is_array($tagA['class']) ){

                $class = join(' ', $tagA['class']);

            }else{

                $class = $tagA['class'];

            }

            // 显示字符画样式
            if(!isset($tagA['iconClass'])){

                $iconClass = '';

            }else if( is_array($tagA['iconClass']) ){

                $iconClass = join(' ', $tagA['iconClass']);

            }else{

                $iconClass = $tagA['iconClass'];

            }


            $title = isset($tagA['title'])?$tagA['title']:'';

            $text  = isset($tagA['text'])?$tagA['text']:'';

            $target  = isset($tagA['target'])?$tagA['target']:'';

            $id    = isset($tagA['id'])?$tagA['id']:'';

            $dataHtml = '';
            if(!empty($data)){
                foreach ($data as $key => $value){
                    $dataHtml .= ' data-'.$key.'="'.$value.'" ';
                }
            }

            $html = '<a href="'.$href.'" '
                . ' title="'.$title.'" '
                . ' id="'.$id.'" '
                . ' class="'.$class.'" '
                . ' target="'.$target.'" '
                . $dataHtml.'>'
                . '<i class="'.$iconClass.'"></i>'.$text.'</a>';

        }else if('divide' == $type){
            $html = '<span><i class="icon icon-none"></i></span>';
        }else{
            $html = '';
        }
        return $html;
    }
}

if(! function_exists('modifyEnv')){
    /**
     * 向.env 环境配置中设置参数
     * @param array $data
     */
    function modifyEnv(array $data){
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use ($data){
            foreach ($data as $key => $value){
                if(str_contains($item, $key)){
                    return $key . '=' . $value;
                }
            }

            return $item;
        });

        $content = implode($contentArray->toArray(), "\n");

        $flag = \Illuminate\Support\Facades\File::put($envPath, $content);

        return  $flag === false?-1:$flag;
    }
}

if (!function_exists('trans_options')) {
    /**
     * trans options扩展
     * @param string $name
     * @param array $keys
     * @return array
     */
    function trans_options($name = '', $keys = [], $lang = 'lang')
    {
        $options = [];

        $emptyOption = '';

        foreach ($keys as $key){

            if(empty($key) &&  0 !== $key)
            {
                $emptyOption =  trans("{$lang}.{$name}.empty")?:'请选择';

                continue;
            }

            $options[$key] =  trans("{$lang}.{$name}.{$key}")?:$key;
        }

        if(!empty($emptyOption))
        {
            $options = array_prepend($options, $emptyOption);
        }

        return $options;
    }
}

if (!function_exists('admin_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('admin_url')) {
    /**
     * Get admin url.
     *
     * @param string $url
     *
     * @return string
     */
    function admin_url($url = '')
    {
        $prefix = trim(config('admin.prefix'), '/');

        return url($prefix ? "/$prefix" : '').'/'.trim($url, '/');
    }
}

if (!function_exists('admin_toastr')) {

    /**
     * Flash a toastr messaage bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array  $options
     *
     * @return string
     */
    function admin_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new \Illuminate\Support\MessageBag(get_defined_vars());

        \Illuminate\Support\Facades\Session::flash('toastr', $toastr);
    }
}

if (!function_exists('workshop_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function workshop_path($path = '')
    {
        return ucfirst(config('workshop.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('workshop_url')) {
    /**
     * Get admin url.
     *
     * @param string $url
     *
     * @return string
     */
    function workshop_url($url = '')
    {
        $prefix = trim(config('workshop.system.prefix'), '/');

        return url($prefix ? "/$prefix" : '').'/'.trim($url, '/');
    }
}

if (!function_exists('workshop_route')) {
    /**
     * Get workshop route.
     *
     * @param string $route
     *
     * @return string
     */
    function workshop_route($route = '')
    {
        $prefix = trim(config('workshop.system.prefix'), '.');

        return $route ? $prefix.'.'.trim($route, '.') : '';
    }
}

if (!function_exists('workshop_toastr')) {

    /**
     * Flash a toastr messaage bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array  $options
     *
     * @return string
     */
    function workshop_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new \Illuminate\Support\MessageBag(get_defined_vars());

        \Illuminate\Support\Facades\Session::flash('toastr', $toastr);
    }
}

if(!function_exists('workshop_trans')){

    /** workshop-trans
     * @param $name
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    function workshop_trans($name){
        return trans("workshop::lang.{$name}");
    }
}

if (!function_exists('workshop_trans_options')) {
    /**
     * trans options扩展
     * @param string $name
     * @param array $keys
     * @return array
     */
    function workshop_trans_options($name = '', $keys = [])
    {
        $options = [];

        foreach ($keys as $key){
            $options[$key] =  workshop_trans("{$name}.{$key}")?:$key;
        }

        return $options;
    }
}

if(!function_exists('workshop_close_layer')){
    function workshop_close_layer()
    {
        return  \Eqinfo\Workshop\Facades\ViewManager::content(function(\Eqinfo\Workshop\View\Layout\Content $content){
            $content->body(view('workshop::adminlte.widgets.closeLayerWindow'));
        });
    }

}

if (!function_exists('workbench_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function workbench_path($path = '')
    {
        return ucfirst(config('workbench.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('workbench_url')) {
    /**
     * Get admin url.
     *
     * @param string $url
     *
     * @return string
     */
    function workbench_url($url = '')
    {
        $prefix = trim(config('workbench.prefix'), '/');

        return url($prefix ? "/$prefix" : '').'/'.trim($url, '/');
    }
}

if (!function_exists('workbench_toastr')) {

    /**
     * Flash a toastr messaage bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array  $options
     *
     * @return string
     */
    function workbench_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new \Illuminate\Support\MessageBag(get_defined_vars());

        \Illuminate\Support\Facades\Session::flash('toastr', $toastr);
    }
}

if(!function_exists('workbench_trans')){

    /** workshop-trans
     * @param $name
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    function workbench_trans($name){
        return trans("workbench::lang.{$name}");
    }
}

if (!function_exists('workbench_trans_options')) {
    /**
     * trans options扩展
     * @param string $name
     * @param array $keys
     * @return array
     */
    function workbench_trans_options($name = '', $keys = [])
    {
        $options = [];

        foreach ($keys as $key){
            $options[$key] =  workbench_trans("{$name}.{$key}")?:$key;
        }

        return $options;
    }
}

if(!function_exists('schema_path')){

    /**
     * @param string $path
     *
     * @return string
     */
    function schema_path($path = '')
    {
        return app()->resourcePath('schemas'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}

/**
 * Trans2rmb
 */
if(! function_exists('trans2rmb')){
    function trans2rmb($num) {
        $rtn = '';
        $num = round($num, 2);

        $s = array(); // 存储数字的分解部分
        //==> 转化为字符串,$s[0]整数部分,$s[1]小数部分
        $s = explode('.', strval($num));

        // 超过12位(大于千亿)则不予处理
        if (strlen($s[0]) > 12)
        {
            return '*'.$num;
        }

        // 中文大写数字数组
        $c_num = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');

        // 保存处理过程数据的数组
        $r = array();

        //==> 处理 分/角 部分
        if (!empty($s[1]))
        {
            $jiao = substr($s[1], 0,1);
            if (!empty($jiao))
            {
                $r[0] .= $c_num[$jiao].'角';
            }
            else
            {
                $r[0] .= '零';
            }

            $cent = substr($s[1], 1,1);
            if (!empty($cent))
            {
                $r[0] .=  $c_num[$cent].'分';
            }
        }else{
            $r[0] = '';
        }

        //==> 数字分为三截,四位一组,从右到左:元/万/亿,大于9位的数字最高位都归为"亿"
        $f1 = 1;
        for ($i = strlen($s[0])-1; $i >= 0; $i--, $f1 ++)
        {
            $f2 = floor(($f1-1)/4)+1; // 第几截
            if ($f2 > 3)
            {
                $f2 = 3;
            }
            $r[$f2] = empty($r[$f2])?'':$r[$f2];
            // 当前数字
            $curr = substr($s[0], $i, 1);

            switch ($f1%4)
            {
                case 1:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr]).$r[$f2];
                    break;
                case 2:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'拾').$r[$f2];
                    break;
                case 3:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'佰').$r[$f2];
                    break;
                case 0:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'仟').$r[$f2];
                    break;
            }
        }

        $rtn .= empty($r[3]) ? '' : $r[3].'亿';
        $rtn .= empty($r[2]) ? '' : $r[2].'万';
        $rtn .= empty($r[1]) ? '' : $r[1].'元';

        $rtn .= $r[0].'整';


        //==> 规则:如果位数为零,在"元"之前不出现"零",在空位处且不在"元"之间的,则填充一个"零"(num为0的情况除外)
        if ($num != 0)
        {
            while(1)
            {
                if (substr_count($rtn, "零零") == 0 && substr_count($rtn, "零元") == 0
                    && substr_count($rtn, "零万") == 0 && substr_count($rtn, "零亿") == 0)
                {
                    break;
                }
                $rtn = str_replace("零零", "零", $rtn);
                $rtn = str_replace("零元", "元", $rtn);
                $rtn = str_replace("零万", "万", $rtn);
                $rtn = str_replace("零亿", "亿", $rtn);
            }
        }
        return $rtn;
    }
}

if(!function_exists('filesize_ext'))
{
    function filesize_ext($filesize) {
        if($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' gb';
        } elseif($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' mb';
        } elseif($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' kb';
        } else {
            $filesize = $filesize . ' bytes';
        }
        return $filesize;
    }
}

/**
 *
 */
if(! function_exists('monthNum')){
    function monthNum( $date1, $date2, $tags='-' ){
        $date1 = explode($tags,$date1);
        $date2 = explode($tags,$date2);
        return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
    }
}



// 如：db:seed 或者 清空数据库命令的地方调用
if(! function_exists('insanity_check')) {
    function insanity_check()
    {
        if (\Illuminate\Support\Facades\App::environment('production')) {
            exit('别傻了? 这是线上环境呀。');
        }
    }
}
if(! function_exists('cdn')) {
    function cdn($filepath)
    {
        if (config('app.url_static')) {
            return config('app.url_static') . $filepath;
        } else {
            return config('app.url') . $filepath;
        }
    }
}

if(! function_exists('get_cdn_domain')) {
    function get_cdn_domain()
    {
        return config('app.url_static') ?: config('app.url');
    }
}

if(! function_exists('get_user_statice_domain')){
    function get_user_static_domain() {
        return config('app.user_static') ?: config('app.url');
    }
}

if(! function_exists('lang')) {
    function lang($text, $parameters = [])
    {
        return str_replace('phphub.', '', trans('phphub.' . $text, $parameters));
    }
}
if(! function_exists('admin_link')) {
    function admin_link($title, $path, $id = '')
    {
        return '<a href="' . admin_url($path, $id) . '" target="_blank">' . $title . '</a>';
    }
}

if(! function_exists('admin_url')) {
    function admin_url($path, $id = '')
    {
        return env('APP_URL') . "/admin/$path" . ($id ? '/' . $id : '');
    }
}

if(! function_exists('admin_enum_style_output')) {
    function admin_enum_style_output($value, $reverse = false)
    {
        if ($reverse) {
            $class = ($value === true || $value == 'yes') ? 'danger' : 'success';
        } else {
            $class = ($value === true || $value == 'yes') ? 'success' : 'danger';
        }

        return '<span class="label bg-' . $class . '">' . $value . '</span>';
    }
}

if(! function_exists('navViewActive')) {
    function navViewActive($anchor)
    {
        return Route::currentRouteName() == $anchor ? 'active' : '';
    }
}

if(! function_exists('model_link')) {
    function model_link($title, $model, $id)
    {
        return '<a href="' . model_url($model, $id) . '" target="_blank">' . $title . '</a>';
    }
}
if(! function_exists('model_url')) {
    function model_url($model, $id)
    {
        return env('APP_URL') . "/$model/$id";
    }
}
if(! function_exists('per_page')) {
    function per_page($default = null)
    {
        $max_per_page = config('api.max_per_page');
        $per_page = (\Illuminate\Support\Facades\Input::get('per_page') ?: $default) ?: config('api.default_per_page');

        return (int)($per_page < $max_per_page ? $per_page : $max_per_page);
    }
}

/**
 * 生成用户客户端 URL Schema 技术的链接.
 */
if(! function_exists('schema_url')) {
    function schema_url($path, $parameters = [])
    {
        $query = empty($parameters) ? '' : '?' . http_build_query($parameters);

        return strtolower(config('app.name')) . '://' . trim($path, '/') . $query;
    }
}

// formartted Illuminate\Support\MessageBag
if(! function_exists('output_msb')) {
    function output_msb(\Illuminate\Support\MessageBag $messageBag)
    {
        return implode(", ", $messageBag->all());
    }
}

if(! function_exists('get_platform')) {
    function get_platform()
    {
        return Request::header('X-Client-Platform');
    }
}

function is_request_from_api()
{
    return $_SERVER['SERVER_NAME'] == env('API_DOMAIN');
}

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function img_crop($filepath, $width = 0, $height = 0)
{
    return $filepath . "?imageView2/1/w/{$width}/h/{$height}";
}


function setting($key, $default = '')
{
    if ( ! config()->get('settings')) {
        // Decode the settings to an associative array.
        $site_settings = json_decode(file_get_contents(storage_path('/administrator_settings/site.json')), true);
        // Add the site settings to the application configuration
        config()->set('settings', $site_settings);
    }

    // Access a setting, supplying a default value
    return config()->get('settings.'.$key, $default);
}
