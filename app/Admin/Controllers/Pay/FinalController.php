<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;

/**
 * Class FinalController
 *
 * 付款计划终稿编辑控制器
 *
 * @package App\Admin\Controllers\Pay
 */
class FinalController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index' => 'payment.schedule.audit',
        'finalEdit' => 'final.schedule.edit',
    ];

    public function index()
    {
        return redirect($this->getUrl('index'));
    }

    public function edit($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('audit.payment.schedule'));
            $content->description('终稿核定');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('index')],
                ['text'=>'终稿核定', 'url'=> $this->getUrl('finalEdit', ['id'=>$id])]
            );

            $form = $this->form()->edit($id);

            $content->body($form);
        });
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }
    /**
     * 终稿核定信息
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
                    ->text('name', trans('payment.schedule.name'))
                    ->readonly();

                $row->width(3)
                    ->select('bill_period_id', trans('payment.schedule.bill_period'))
                    ->options(PaymentSchedule::getBillPeriodOptions())
                    ->readonly();

                $row->width(3)
                    ->select('payment_type_id', trans('payment.schedule.payment_type'))
                    ->options(PaymentSchedule::getPaymentTypeOptions())
                    ->readonly();
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
                    ->readonly();

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
                    ->readonly();
            });
            $form->divider();
            // 其他导入信息
            $form->row(function (Form\Row $row) use ($form)
            {

                $row->width(6)
                    ->text('pay_cycle', trans('payment.schedule.pay_cycle'))
                    ->readonly();
                $row->width(6)
                    ->text('charge_man', trans('payment.schedule.charge_man'))
                    ->readonly();
            });

            //金额调整
            $form->row(function (Form\Row $row) use ($form)
            {
                $row->width(6)
                    ->currency('supplier_balance', trans('payment.schedule.supplier_balance'))
                    ->prepend('￥')
                    ->readonly();
                $row->width(6)
                    ->currency('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'))
                    ->prepend('￥')
                    ->readonly();

                $row->width(12)->divider();

                $row->width(6)
                    ->text('plan_man', trans('payment.schedule.plan_man'))
                    ->readonly();
                $row->width(3)
                    ->date('plan_time', trans('payment.schedule.plan_time'))
                    ->readonly();
                $row->width(3)
                    ->currency('plan_due_money', trans('payment.schedule.plan_due_money'))
                    ->prepend('￥')
                    ->readonly();
                $row->width(12)
                    ->textarea('memo', trans('admin.memo'))
                    ->rows(1)
                    ->readonly();
            });

            // 初稿核定调整
            $form->row(function (Form\Row $row) use ($form)
            {

                $row->width(12)->divide();
                $row->width(6)
                    ->text('audit_man', trans('payment.schedule.audit_man'))
                    ->readonly();
                $row->width(3)
                    ->date('audit_time', trans('payment.schedule.audit_time'))
                    ->readonly();
                $row->width(3)
                    ->currency('audit_due_money', trans('payment.schedule.audit_due_money'))
                    ->prepend('￥')
                    ->readonly();
                $row->width(12)
                    ->textarea('memo_audit', trans('admin.memo'))
                    ->rows(2)
                    ->readonly();
            });

            // 终稿核定调整
            $form->row(function (Form\Row $row) use ($form)
            {
                $row->width(12)->divide();

                $row->width(6)
                    ->text('final_man', trans('payment.schedule.final_man'))
                    ->rules('nullable');
                $row->width(3)
                    ->date('final_time', trans('payment.schedule.final_time'))
                    ->format('YYYY-MM-DD')
                    ->default(Carbon::now());
                $row->width(3)
                    ->currency('final_due_money', trans('payment.schedule.final_due_money'))
                    ->prepend('￥')
                    ->rules('required');
                $row->width(12)
                    ->textarea('memo_final', trans('admin.memo'))
                    ->rules('nullable');

                $row->width(0)
                    ->hidden('status');
            });

            $form->saving(function(Form $form){
                if(empty($form->final_man))
                {
                    $form->final_man =  Admin::user()->name;
                }
                $form->status = PaymentSchedule::STATUS_CHECK_FINAL;
            });

            $form->ignore(['bill_period_id','supplier_id', 'payment_type_id', 'payment_materiel_id']);
        });
    }
}
