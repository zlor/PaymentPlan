<?php

namespace App\Admin\Controllers;

use App\Models\BillPeriod;
use App\Models\PaymentSchedule;

use App\Models\UserEnv;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PaymentScheduleController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('payment.schedule'));
            $content->description(trans('admin.list'));

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

            $content->header(trans('payment.schedule'));
            $content->description(trans('admin.edit'));

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

            $content->header(trans('payment.schedule'));
            $content->description(trans('admin.create'));

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

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.bill_period'));

            // // 导入信息
            // $grid->column('importInfo', '导入信息')->display(function(){
            //     return "<pre>
            //          供应商名称/物料名称/ 总应付款/ 计划付款/计划人 /导入时间
            //     </pre>";
            // });
            // // 审核信息
            // // 终稿信息
            // // 付款信息
            // // 状态


            // // 供应商名称(导入)
            $grid->column('supplier_name', trans('payment.schedule.supplier_name'));

            // 供应商(匹配)
            $grid->column('supplier.name', trans('payment.schedule.supplier'));

            // 供应商余额
            $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'));

            // 应付款
            $grid->column('due_money', trans('payment.schedule.due_money'));

            // 计划应付款
            $grid->column('plan_due_money', trans('payment.schedule.plan_due_money'));

            // 已付金额
            $grid->column('paid_money', trans('payment.schedule.paid_money'))
                ->display(function(){
                    return $this->paid_money;
                });

            // 已付现金
            $grid->column('cash_paid', trans('payment.schedule.cash_paid'));

            // 已付承兑
            $grid->column('acceptance_paid', trans('payment.schedule.acceptance_paid'));

            // 状态
            $grid->column('status', trans('payment.schedule.status'))
                ->display(function($value){
                    return trans('payment.schedule.status.' . $value);
                });

            // 导入时间
            $grid->column('plan_time', trans('payment.schedule.plan_time'));

            // 导入批次
            $grid->column('batch', trans('payment.schedule.batch'));


            // filter 过滤器
            $grid->filter(function(Grid\Filter $filter){
                $filter->disableIdFilter();

                // 账期
                $filter->equal('bill_period_id', trans('payment.schedule.bill_period'))
                    ->select(PaymentSchedule::getBillPeriodOptions())
                    ->default(strval(BillPeriod::getCurrentId()));

                // 类型
                $filter->equal('payment_type_id', trans('payment.type'))
                    ->select(PaymentSchedule::getPaymentTypeOptions());

                // 科目
                $filter->like('name', trans('payment.schedule.name'));

                // 供应商
                $filter->like('supplier_name', trans('payment.schedule.supplier_name'));

                //

            });

            // $grid->created_at();
            // $grid->updated_at();
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

            $billPeriodId = BillPeriod::getCurrentId();

            // 账期
            $form->select('bill_period_id', trans('payment.schedule.bill_period'))
                 ->options(PaymentSchedule::getBillPeriodOptions())
                 ->default($billPeriodId)
                 ->rules('required');

            // 供应商(系统匹配)
            $form->select('supplier_id', trans('payment.schedule.supplier'))
                ->options(PaymentSchedule::getSupplierOptions());

            // 供应商(原始名称)
            $form->text('supplier_name', trans('payment.schedule.supplier_name'));

            // 付款类型
            $form->select('payment_type_id', trans('payment.schedule.payment_type'))
                ->options(PaymentSchedule::getPaymentTypeOptions());

            // 付款物料(系统匹配)
            $form->select('payment_materiel_id', trans('payment.schedule.payment_materiel'))
                ->options(PaymentSchedule::getPaymentMaterielOptions());

            // 付款物料(导入名称)
            $form->text('materiel_name', trans('payment.schedule.materiel_name'));


            // 付款计划流水
            $form->text('name', trans('payment.schedule.name'));

            // 付款计划状态
            $form->select('status', trans('payment.schedule.status'))
                ->options(PaymentSchedule::getStatusOptions('payment.schedule', ['init','import_init', 'web_init', 'checked','paying','lock' ]))
                ->default('web_init');

            $form->divider();

            // 供应商余额
            $form->currency('supplier_balance', trans('payment.schedule.supplier_balance'));

            // 上期未付清余款
            $form->currency('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'));

            // 应付款
            $form->currency('due_money', trans('payment.schedule.due_money'));

            $form->divider();

            // 已支付
            $form->currency('paid_money', trans('payment.schedule.paid_money'))
                ->readOnly();
            // 已支付现金
            $form->currency('cash_paid', trans('payment.schedule.cash_paid'))
                ->readOnly();
            // 已支付承兑
            $form->currency('acceptance_paid', trans('payment.schedule.acceptance_paid'))
                ->readOnly();

            $form->ignore(['paid_money','cash_paid', 'acceptance_paid']);

            $form->divider();

            // 计划时间
            $form->date('plan_time', trans('payment.schedule.plan_time'));

            // 导入批次
            $form->display('batch', trans('payment.schedule.batch'));


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
