<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

/**
 * Class AuditController
 *
 * 付款计划审核用控制器
 *
 * @package App\Admin\Controllers\Pay
 */
class AuditController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'check' => 'payment.audit.index',

    ];

    public function index(Request $request)
    {
        return Admin::content(function(Content $content){

            $content->header(trans('audit.payment.schedule'));
            $content->description(trans('admin.list'));

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('check')]
            );

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(PaymentSchedule::class, function(Grid $grid){

            $grid->column('name', trans('payment.schedule.name'));

            //增加审核操作行
            // 导入信息
            $grid->column('importInfo', trans('payment.schedule.importInfo'))
                ->display(function(){

                    // TODO 构造一个小视图
                    return '';
                });

            $grid->column('audit_due_money', trans('payment.schedule.audit_due_money'));//->editable('audit_due_money');

            // 默认条件
            $grid->filter(function(Grid\Filter $filter){

                $filter->disableIdFilter();

                // 账期
                $filter->equal('bill_period_id', trans('audit.payment.schedule.bill_period'))
                        ->select(PaymentSchedule::getBillPeriodOptions())
                        // Tips select
                        ->default(strval(BillPeriod::getCurrentId()));

                // 科目内容
                $filter->like('name', trans('audit.payment.schedule.name'))->default('sd');
            });

        });
    }
}
