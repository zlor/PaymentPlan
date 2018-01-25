<?php

namespace App\Admin\Controllers\Pay;

use App\Admin\Extensions\Tools\Import;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use App\Models\UserEnv;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ScheduleController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index'  => 'plan.schedule',
        'excel'  => 'payment.plan.excel',
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('付款计划');
            $content->description('导入并编辑');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划录入', 'url'=> $this->getUrl('index')]
            );

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(PaymentSchedule::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            /**
             * 暂不提供的按钮
             *
             * 创建、导出
             */
            $grid->disableCreation();
            $grid->disableExport();

            // 设置默认账期
            $defaultBillPeriod = BillPeriod::envCurrent();

            if($defaultBillPeriod)
            {
                $grid->model()->where('bill_period_id', $defaultBillPeriod->id);
            }

            $grid->filter(function(Grid\Filter $filter)use($defaultBillPeriod){

                $filter->disableIdFilter();

                // 账期
                $filter->equal('bill_period_id', trans('payment.schedule.bill_period'))
                    ->select(PaymentSchedule::getBillPeriodOptions())
                    ->default(strval($defaultBillPeriod->id));

                // 分类
                $filter->equal('payment_type_id', trans('payment.type'))
                    ->select(PaymentSchedule::getPaymentTypeOptions());

                $filter->like('name', trans('payment.schedule.name'));
            });

            /**
             * 工具栏
             *
             * 增加导入链接
             *
             */
            $grid->tools(function(Grid\Tools $tools){

                $tool_import = new Import();

                $tool_import->setAction($this->getUrl('excel'));

                $tools->append($tool_import);

            });

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.bill_period'));
            // 供应商(匹配)
            $grid->column('supplier.name', trans('payment.schedule.supplier'))->popover('right', ['limit'=>15]);

            /**
             * 导入信息汇总
             */
            $grid->column('import_info', trans('payment.schedule.importInfo'))
                ->display(function(){
                    // 科目编号
                    // 供应商名称
                    // 物料类型
                    // 物料名称
                    return 'test';
                });

            // 科目编号
            $grid->column('name', trans('payment.schedule.name'));



            // 类型说明
            $grid->column('payment_type.name', trans('payment.schedule.payment_type'));

            // 供应商名称(导入)
            $grid->column('supplier_name', trans('payment.schedule.supplier_name'));



            // 供应商余额
            $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'));

            // 应付款
            $grid->column('due_money', trans('payment.schedule.due_money'));

            // 已付金额
            $grid->column('paid_money', trans('payment.schedule.paid_money'))
                ->display(function(){
                    return $this->paid_money;
                });

            // 已付现金
            $grid->column('cash_paid', trans('payment.schedule.cash_paid'));

            // 已付承兑
            $grid->column('acceptance_paid', trans('payment.schedule.acceptance_paid'));

            // 计划时间
            $grid->column('plan_time', trans('payment.schedule.plan_time'));

            // 状态
            $grid->column('status', trans('payment.schedule.status'))
                ->display(function($value){
                    return trans('payment.schedule.status.' . $value);
                });

            // 导入批次
            $grid->column('batch', trans('payment.schedule.batch'));


            // filter 过滤器

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(PaymentSchedule::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
