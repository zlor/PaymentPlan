<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class TransMultiRow extends AbstractDisplayer
{

    public function display($key='')
    {
        $pre = '';
        if(!empty($key))
        {
            $pre = $key.'.';
        }
        $text = '';
        if(!empty($this->value))
        {
            $text =  trans("{$pre}{$this->value}");
        }

        return $text;
    }

}