<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

/**
 * Class DetailController
 *
 * 付款管理-付款明细入口
 *
 * @package App\Admin\Controllers\Pay
 */
class DetailController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index' => 'pay.schedule.index'
    ];

    public function index()
    {
        return Admin::content(function(Content $content){

            $content->header('');

            $content->description('');

            $content->body($this->grid());

        });
    }

    protected function grid()
    {
        return Admin::grid(PaymentSchedule::class, function(Grid $grid){
            $grid->filter(function(Grid\Filter $filter){
                $filter->disableIdFilter();
                $filter->like('name', trans('payment.schedule.name'));
            });
        });
    }
}
