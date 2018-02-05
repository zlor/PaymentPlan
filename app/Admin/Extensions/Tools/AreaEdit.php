<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class AreaEdit extends AbstractTool
{

    protected $action;

    protected $area_name;

    protected $head_name;

    protected $input_type;

    public function __construct($areaName = 'area', $headName = 'head')
    {
        $this->area_name = $areaName;
        $this->head_name = $headName;
    }

    protected function script()
    {
        // $url = Request::fullUrlWithQuery(['gender' => '_gender_']);
        $areaClass = $this->area_name;
        $headClass = $this->head_name;

        $inputType = $this->input_type;

        $options = [];

        if(!empty($inputType))
        {
            if($inputType == 'currency')
            {
                $options = [
                    'alias'              => 'currency',
                    'radixPoint'         => '.',
                    'prefix'             => '',
                    'removeMaskOnSubmit' => true,
                ];
            }
        }

        Admin::js(asset('/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js'));

        $options = json_encode($options);

        $sub_init_script =<<<SCRIPT
            $('td span.edit.{$areaClass} input[name="offset[]"]').inputmask($options);
SCRIPT;

        // 开启栏目编辑模式
        return <<<EOT
var initMode = 0;
// 启用编辑模式
$('.btn-area-edit').click(function(){
    if($(this).hasClass('active'))
    {
        $('#filter_btn').click();
        $(this).removeClass('active');
        $('td span.{$areaClass}').removeClass('edit');
    }else{
        $('#filter_btn').click();
        $(this).addClass('active');
        $('td span.{$areaClass}').addClass('edit');
        initEdit();
    }
});
// 实时保存反馈
var postCell = function(target){
    var url = target.find('div.action').data('url'),
        offset = target.find('div.action input[name="offset[]"]').inputmask('unmaskedvalue'),
        origin = target.find('div.info').data('origin'),
        params = {};
    
    // 若值未变动,则不提交
    if(origin == offset)
    {   
        return false;
    }
    
    // 获取改动后的信息
    params['money'] = offset;
    params['_token'] = LA.token;
    params['_method'] = 'PUT';
    // 提交保存
    $.post(url, params, function(data){
        // 反馈保存信息
        if(data.success)
        {
            target.find('div.info').data('origin', data.money);
            target.find('div.info label.text-money').text(data.money_format);
            // 设置保存成功样式
            target.find('div.action i.post-result').addClass('text-green fa-check');
            
            target.find('div.action input').removeClass('text-warning');
            // 触发表格内合计
            reCount();
        }else{
            target.find('div.action i.post-result').removeClass('text-green').removeClass('fa-undo');
            target.find('div.action input').addClass('text-warning');
        }
    }, 'json')
};

var reCount = function(){
    var sum = 0;
    var headInfoCell = $('tr.counter td span.{$headClass}');
     
    $('td span.{$areaClass} li div.info').each(function(){
        sum += 100 * $(this).data('origin');
    });
    
    sum = sum.toFixed(0) /100;
    
    console.log([headInfoCell, sum]);

    $('li div.info', headInfoCell).data('origin', sum);
    $('li div.info label.text-money', headInfoCell).text(number_format(sum, 2, ','));
}
// 初始化编辑
function initEdit(){
    if(!initMode)
    {
        {$sub_init_script}
        
        initTabSwitch();
        
        initMode = true;
    }
};

// 设置快速切换和操作
function initTabSwitch(){
        var targetInputs = $('td span.edit.{$areaClass} input[name="offset[]"]');
        var length = targetInputs.length;
        
            targetInputs.keydown(function(e) {
               
                var idx = targetInputs.index(this); // 获取当前焦点输入框所处的位置
                
                var which = e.which,
                    needSwitch =  false,
                    switchIndex = idx;
                
                if(e.shiftKey && which == 9){ // Shift + Tab
                    
                    needSwitch = true;
                      
                    if (idx == 0) {
                        // 判断是否是最后一个输入框
                        switchIndex = length - 1;
                    } else {
                        switchIndex = idx - 1;
                    }
                    
                }else if (which == 9 || which == 13) {
                    // 判断所按是否 Tab键 或 回车键
                    needSwitch = true;

                    if (idx == length - 1) {
                        // 判断是否是最后一个输入框
                        switchIndex = 0;
                    } else {
                        switchIndex = idx + 1;  
                    }
                }
                 // console.log([needSwitch, switchIndex]);         
                if(needSwitch)
                {
                    postCell($(this).parents('span.edit.{$areaClass}'));
                    targetInputs[switchIndex].focus(); // 设置焦点  
                    targetInputs[switchIndex].select(); // 选中文字
                    
                    // 阻止默认事件
                    return false;
                }
            });
        targetInputs[0].focus();  // 设置焦点
        targetInputs[0].select(); // 选中文字
};

/**
 * number_format
 * @param number 传进来的数,
 * @param bit 保留的小数位,默认保留两位小数,
 * @param sign 为整数位间隔符号,默认为空格
 * @param gapnum 为整数位每几位间隔,默认为3位一隔
 * @type arguments的作用：arguments[0] == number(之一)
*/
function number_format(number,bit,sign,gapnum){
    //设置接收参数的默认值
    var bit    = arguments[1] ? arguments[1] : 2 ;
    var sign   = arguments[2] ? arguments[2] : ' ' ;
    var gapnum = arguments[3] ? arguments[3] : 3 ;
    var str    = '' ;

    number     = number.toFixed(bit);//格式化
    realnum    = number.split('.')[0];//整数位(使用小数点分割整数和小数部分)
    decimal    = number.split('.')[1];//小数位
    realnumarr = realnum.split('');//将整数位逐位放进数组 ["1", "2", "3", "4", "5", "6"]
    
    //把整数部分从右往左拼接，每bit位添加一个sign符号
    for(var i=1;i<=realnumarr.length;i++){
        str = realnumarr[realnumarr.length-i] + str ;
        if(i%gapnum == 0){
            str = sign+str;//每隔gapnum位前面加指定符号
        }
    }
    
    //当遇到 gapnum 的倍数的时候，会出现比如 ",123",这种情况，所以要去掉最前面的 sign
    str = (realnum.length%gapnum==0) ? str.substr(1) : str;
    //重新拼接实数部分和小数位
    realnum = str+'.'+decimal;
    return realnum;
}

EOT;
// $('td span.{$areaClass}').change(function () {
//
//     var url = "$url".replace('_gender_', $(this).val());
//
//     $.pjax({container:'#pjax-container', url: url });
//
// });
    }

    public function setInputType($type = 'currency')
    {
        $this->input_type = $type;
    }

    public function setAction($action)
    {

        $this->action = $action;

        return $this;
    }

    public function render()
    {
        Admin::script($this->script());


        $action = $this->action;

        return view('admin::tools.area_edit', compact('action'));
    }
}