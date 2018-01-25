<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use Illuminate\Support\Str;

class Currency extends AbstractDisplayer
{

    public function display($formatToNumber = false)
    {
        return $formatToNumber? str_replace(',', '', $this->value) : number_format($this->value);
    }
}