<?php
namespace App\Http\Controllers\Traits;


trait GetSpanMoney
{

    protected function _getMoneySpan($money, $options = [])
    {
        $zeroFlag = intval(100 * $money);

        $classMoney = 'text-money';

        if($zeroFlag == 0)
        {
            $classMoney .= ' text-money-zero ';
        }
        else if($zeroFlag < 0)
        {
            $classMoney .= ' text-money-minius ';
        }

        $classPriority = isset($options['moneyClass'])?$options['moneyClass']:' ';

        $classLi = isset($options['liClass'])?$options['liClass']:' ';

        $classArea = isset($options['spanClass'])?$options['spanClass']:' ';

        $title = isset($options['title'])?$options['title']:'';

        $url = isset($options['url'])?$options['url']:'';

        $needAction = isset($options['action']);

        $txt = number_format($money, 2);

        return "<span class='{$classArea}'><ul class='ul-area list-unstyled' style='margin: auto'>
                        <li class='text-right {$classLi}'>
                            <div class='info' data-origin='{$money}' data-toggle='tooltip' data-title='{$title}'>
                                <i class='coin'>￥</i>
                                <label class='bg-white {$classMoney} {$classPriority}'>{$txt}</label>
                            </div>
                ".
            ($needAction ? "<div class='action pull-right' data-url='{$url}'><i class='post-result fa'></i><input size='12' class='text-right' name='offset[]' type='text' value='{$money}' ></div>"
                : "" )
            ."        
                        </li>
                </ul></span>";
    }
}