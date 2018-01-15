<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Widget;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            // TODO 显示系统当前重要信息

            $content->header('');
            $content->description('');

            $content->row($this->account_period());


        });
    }


    protected function account_period()
    {
        // return new InfoBox('账期', 'fa fa-bill', 'blue','', '七月');
        $boxAccountPeriod = new Box('账期', '一月');

        return $boxAccountPeriod;
    }
}
