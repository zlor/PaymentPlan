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
     * 计划录入
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
     * 调整计划
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('付款计划');
            $content->description('调整计划');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划录入', 'url'=> $this->getUrl('index')],
                ['text'=>'调整计划']
            );

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * TODO 暂不开放
     *
     * 新增计划
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

            /**
             * 操作行
             *
             * 初始化状态的允许调整记录
             *
             */
            $grid->actions(function(Grid\Displayers\Actions $actions){

                $paymentSchedule = $this->row;

                if( ! $paymentSchedule->allowPlanEdit())
                {
                    $actions->disableEdit();
                    $actions->disableDelete();
                }


            });

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.bill_period'));
            // 物料类型
            $grid->column('payment_type.name', trans('payment.schedule.payment_type'));
            // // 供应商(匹配)
            // $grid->column('supplier.name', trans('payment.schedule.supplier'))->popover('right', ['limit'=>15]);
            // // 物料名称(匹配)
            // $grid->column('payment_materiel.name', trans('payment.schedule.payment_materiel'));

            /**
             * 导入信息汇总
             */
            $grid->column('import_info', trans('payment.schedule.importInfo'))
                ->display(function(){
                    // 科目编号
                    $title_name = trans('payment.schedule.name');
                    // 供应商名称
                    $title_supplierName = trans('payment.schedule.supplier_name');
                    // 物料名称
                    $title_materiel = trans('payment.schedule.payment_materiel');
                    // 付款周期
                    $title_pay_cycle = trans('payment.schedule.pay_cycle');
                    // 付款确认人
                    $title_charge_man = trans('payment.schedule.charge_man');

                    return "<div>
                                <label class='badge badge-default' title='{$title_supplierName}'>{$this->supplier_name}</label><br>
                                <label class='label label-default' title='$title_name'>{$this->name}</label>
                                <label class='label label-default' title='{$title_materiel}'>{$this->payment_materiel_name}</label>
                                <label class='label label-default' title='{$title_charge_man}'>{$this->charge_man}</label> <label class='label label-default' title='{$title_pay_cycle}'>{$this->pay_cycle}</label>
                                
                           </div>";
                });

            // 供应商余额(总应付款)
            $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'))
                ->currency();

            // 计划应付
            $grid->column('plan_due_money', trans('payment.schedule.plan_due_money'))
                 ->display(function($value){
                     $value = number_format($value);
                     return "<div>
                                <label class='' data-toggle='tooltip' data-title='{$this->plan_man} ({$this->plan_time})'>{$value}</label><br>
                           </div>";
                 });
            // 应付款
            $grid->column('due_money', trans('payment.schedule.due_money'));

            // 已付金额
            $grid->column('paid_money', trans('payment.schedule.paid_money'))
                ->display(function(){
                    return $this->paid_money;
                });
            // 状态
            $grid->column('status', trans('payment.schedule.status'))
                ->display(function($value){
                    return trans('payment.schedule.status.' . $value);
                });

            // // 付款周期
            // $grid->column('pay_cycle', trans('payment.schedule.pay_cycle'));

            // // 已付现金
            // $grid->column('cash_paid', trans('payment.schedule.cash_paid'));
            //
            // // 已付承兑
            // $grid->column('acceptance_paid', trans('payment.schedule.acceptance_paid'));

            // // 计划时间
            // $grid->column('plan_time', trans('payment.schedule.plan_time'));
            // // 导入批次
            // $grid->column('batch', trans('payment.schedule.batch'));

            // $grid->column('at_time', trans('admin.at_time'))
            //     ->display(function(){
            //         return $this->updated_at;
            //     });
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


            $form->row(function(Form\Row $row) use($form){
                $row->width(6)
                    ->display('id', 'ID');
                $row->width(3)
                    ->display('created_at', trans('admin.created_at'));
                $row->width(3)
                    ->display('updated_at', trans('admin.updated_at'));
            });

            // 显示导入的信息
            $form->row(function (Form\Row $row) use ($form)
            {
                $row->width(6)
                    ->text('name', trans('payment.schedule.name'));

                $row->width(3)
                    ->select('bill_period_id', trans('payment.schedule.bill_period'))
                    ->options(PaymentSchedule::getBillPeriodOptions())
                    ->rules('required');

                $row->width(3)
                    ->select('payment_type_id', trans('payment.schedule.payment_type'))
                    ->options(PaymentSchedule::getPaymentTypeOptions())
                    ->rules('required');
            });

            // 供应商信息调整
            $form->row(function (Form\Row $row) use ($form)
            {
                //->rules('required|unique:empl_master,fiscal_id,');
                $row->width(6)
                    ->text('supplier_name', '导入数据:'.trans('payment.schedule.supplier_name'))
                    ->readonly();
                $row->width(6)
                    ->select('supplier_id', '匹配:'.trans('payment.schedule.supplier'))
                    ->options(PaymentSchedule::getSupplierOptions())
                    ->rules('required');

            });
            // 物料信息调整
            $form->row(function (Form\Row $row) use ($form)
            {
                //->rules('required');
                $row->width(6)
                    ->text('materiel_name', '导入数据:'.trans('payment.schedule.materiel_name'))
                    ->readonly();
                $row->width(6)
                    ->select('payment_materiel_id', '匹配:'.trans('payment.schedule.payment_materiel'))
                    ->options(PaymentSchedule::getPaymentMaterielOptions())
                    ->rules('required');
            });
            $form->divider();
            // 其他导入信息
            $form->row(function (Form\Row $row) use ($form)
            {

                $row->width(6)
                    ->text('pay_cycle', trans('payment.schedule.pay_cycle'));
                $row->width(6)
                    ->text('charge_man', trans('payment.schedule.charge_man'));
            });

            //金额调整
            $form->row(function (Form\Row $row) use ($form)
            {
                $row->width(3)
                    ->currency('supplier_balance', trans('payment.schedule.supplier_balance'))
                        ->prepend('￥');
                $row->width(3)
                    ->currency('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'))
                    ->prepend('￥');
                $row->width(6)
                    ->currency('plan_due_money', trans('payment.schedule.plan_due_money'))
                    ->prepend('￥');

                $row->width(12)
                    ->textarea('memo', trans('admin.memo'))
                    ->rules('nullable');
            });
        });
    }
}
