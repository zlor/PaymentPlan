<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class ConfigController extends Controller
{
    use ModelForm;

    public function indexEnvs()
    {
        return Admin::content(function (Content $content) {

            $content->header(__('admin.descript'));
            $content->description(__('admin.description'));

            $content->breadcrumb(
                ['text' => '后台管理', 'url' => '#'],
                ['text'=>'系统参数' ,'url'=>$this->getUrl('self')]
            );

            $content->row($this->title());

            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
        });
    }


    protected function title()
    {
        return view('admin.base.envs_title');
    }
}
