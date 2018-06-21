<?php

namespace App\Admin\Controllers\Collect;

use App\Admin\Extensions\Tools\Import;
use App\Models\BillPeriod;
use App\Models\InvoicePayment;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class InvoiceController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index'  => 'collect.invoice',
        'import'  => 'collect.invoice.excel',
    ];

    /**
     *  发票列表
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('应收款发票');
            $content->description('列表');

            $content->breadcrumb(
                ['text'=>'收款管理', 'url'=>'#'],
                ['text'=>'应收款发票', 'url'=> $this->getUrl('index')]
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

            $content->header('应收款发票');
            $content->description('编辑');

            $content->breadcrumb(
                ['text'=>'收款管理', 'url'=>'#'],
                ['text'=>'应收款发票', 'url'=> $this->getUrl('index')],
                ['text'=>'编辑']
            );

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * 新增应收款发票
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('应收款发票');
            $content->description('新增');

            $content->breadcrumb(
                ['text'=>'收款管理', 'url'=>'#'],
                ['text'=>'应收款发票', 'url'=> $this->getUrl('index')],
                ['text'=>'新增']
            );

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
        return Admin::grid(InvoicePayment::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            /**
             * 暂不提供的按钮
             *
             * 创建、导出
             */
            // $grid->disableCreation();
             $grid->disableExport();

            // 设置默认账期
            $defaultBillPeriod = BillPeriod::envCurrent();

            $grid->filter(function(Grid\Filter $filter)use($defaultBillPeriod){

                $filter->disableIdFilter();

                // 供应商
                $filter->like('supplier.name', trans('invoice.supplier'));

                // 时间
                $filter->between('date', trans('invoice.date'))
                    ->datetime(['format'=>'YYYY-MM-DD']);

                // 发票凭据
                $filter->like('code', trans('invoice.code'));

                // 发票抬头
                $filter->like('title', trans('invoice.title'));
            });

            /**
             * 工具栏
             *
             * 增加导入链接
             *
             */
            $grid->tools(function(Grid\Tools $tools){

//                $tool_import = new Import();
//
//                $tool_import->setAction($this->getUrl('import'));
//
//                $tools->append($tool_import);

            });

//            /**
//             * 操作行
//             *
//             * 初始化状态的允许调整记录
//             *
//             */
//            $grid->actions(function(Grid\Displayers\Actions $actions){
//
//                $paymentSchedule = $this->row;
//
//                if( ! $paymentSchedule->allowPlanEdit())
//                {
//                    $actions->disableEdit();
//                    $actions->disableDelete();
//                }
//
//            });
            //  发票编码
            $grid->column('code', trans('invoice.code'));

            // 发票抬头
            $grid->column('title', trans('invoice.title'));

            // // 供应商(匹配)
            $grid->column('supplier.name', trans('invoice.supplier'))
                ->display(function($value){
                    $txt = mb_strlen($value)>15?(mb_substr($value, 0, 15).'...'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$txt}</span>";
                });

            // 发票时间
            $grid->column('date', trans('invoice.date'));

            // 发票金额
            $grid->column('money', trans('invoice.money'));

//            // 发票已付金额
//            $grid->column('money_paid', trans('invoice.money_paid'));

            // 发票备注
            $grid->column('memo', trans('invoice.memo'));


            // 操作人
            $grid->column('user.name', trans('invoice.user.name'));


//            // 物料类型
//            $grid->column('payment_type.name', trans('payment.schedule.payment_type'));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(InvoicePayment::class, function (Form $form) {


            $form->text('code', trans('invoice.code'))
                ->rules('required');


            $form->select('supplier_id', trans('invoice.supplier'))
                ->options(InvoicePayment::getSupplierOptions());

            $form->text('title', trans('invoice.title'));

            $form->date('date', trans('invoice.date'));


            $form->currency('money', trans('invoice.money'))
                     ->rules('required')
                     ->symbol('￥');

            $form->textarea('memo',  trans('invoice.memo'));

            $form->hidden('user_id');

            $form->saving(function(Form $form){
                if(empty($form->id))
                {
                    $user = Admin::user();

                    $form->user_id = $user->id;
                }
            });

//            $form->row(function(Form\Row $row) use($form){
//                $row->width(3)
//                    ->display('id', 'ID');
//
//                $row->width(3)
//                    ->display('plan_man', trans('payment.schedule.plan_man'))
//                    ->default(strval(UserEnv::getEnv('username')));
//                $row->width(0)
//                    ->hidden('plan_man')->default(strval(UserEnv::getEnv('username')));
//
//                $row->width(3)
//                    ->display('created_at', trans('admin.created_at'));
//                $row->width(3)
//                    ->display('updated_at', trans('admin.updated_at'));
//            });
//
//            // 显示导入的信息
//            $form->row(function (Form\Row $row) use ($form)
//            {
//                $row->width(6)
//                    ->text('name', trans('payment.schedule.name'));
//
//                $row->width(3)
//                    ->select('bill_period_id', trans('payment.schedule.bill_period'))
//                    ->options(PaymentSchedule::getBillPeriodOptions())
//                    ->rules('required');
//
//                $row->width(3)
//                    ->select('payment_type_id', trans('payment.schedule.payment_type'))
//                    ->options(PaymentSchedule::getPaymentTypeOptions())
//                    ->rules('required');
//            });
//
//            // 供应商信息调整
//            $form->row(function (Form\Row $row) use ($form)
//            {
//                //->rules('required|unique:empl_master,fiscal_id,');
//                $row->width(6)
//                    ->text('supplier_name', '导入数据:'.trans('payment.schedule.supplier_name'))
//                    ->readonly();
//                $row->width(6)
//                    ->select('supplier_id', '匹配:'.trans('payment.schedule.supplier'))
//                    ->options(PaymentSchedule::getSupplierOptions())
//                    ->rules('required');
//
//            });
//            // 物料信息调整
//            $form->row(function (Form\Row $row) use ($form)
//            {
//                //->rules('required');
//                $row->width(6)
//                    ->text('materiel_name', '导入数据:'.trans('payment.schedule.materiel_name'))
//                    ->readonly();
//                $row->width(6)
//                    ->select('payment_materiel_id', '匹配:'.trans('payment.schedule.payment_materiel'))
//                    ->options(PaymentSchedule::getPaymentMaterielOptions())
//                    ->rules('required');
//            });
//            $form->divider();
//            // 其他导入信息
//            $form->row(function (Form\Row $row) use ($form)
//            {
//
//                $row->width(6)
//                    ->text('pay_cycle', trans('payment.schedule.pay_cycle'));
//                $row->width(6)
//                    ->text('charge_man', trans('payment.schedule.charge_man'));
//            });
//
//            //金额调整
//            $form->row(function (Form\Row $row) use ($form)
//            {
//                $row->width(3)
//                    ->currency('supplier_balance', trans('payment.schedule.supplier_balance'))
//                        ->prepend('￥');
//                $row->width(3)
//                    ->currency('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'))
//                    ->prepend('￥');
//                $row->width(3)
//                    ->currency('plan_due_money', trans('payment.schedule.plan_due_money'))
//                    ->prepend('￥');
//                $row->width(3)
//                    ->date('plan_time', trans('payment.schedule.plan_time'));
//
//
//                $row->width(12)
//                    ->textarea('memo', trans('admin.memo'))
//                    ->rules('nullable');
//            });
        });
    }
}
