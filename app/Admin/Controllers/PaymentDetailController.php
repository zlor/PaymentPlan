<?php

namespace App\Admin\Controllers;

use App\Models\PaymentDetail;

use URL;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PaymentDetailController extends Controller
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

            $content->header(trans('payment.detail'));
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

            $content->header(trans('payment.detail'));
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

            $content->header(trans('payment.detail'));
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
        return Admin::grid(PaymentDetail::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            // 账期
            $grid->column('bill_period.name', trans('payment.detail.bill_period'));

            // 付款时间
            $grid->column('created_at', trans('payment.detail.time'));

            // 付款金额
            $grid->column('money', trans('payment.detail.money'));

            // 收款公司
            $grid->column('collecting_company', trans('payment.detail.collecting_company'));

            // 付款流水号
            $grid->column('code', trans('payment.detail.code'));

            // 收款供应商
            $grid->column('supplier.name', trans('payment.detail.supplier'));

            // 操作人
            $grid->column('user.name', trans('payment.detail.user'));

            // // 收款证明
            // $grid->column('collecting_proof', trans('payment.detail.collecting_company'))
            //     ->display(function($value) {
            //         return "<img src='{$value}'>";
            //     });

            // 付款证明
            // $grid->column('payment_proof', trans('payment.detail.payment_proof'))
            //     ->display(function($value) {
            //         return "<img src='{$value}'>";
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
        return Admin::form(PaymentDetail::class, function (Form $form) {

            $form->display('id', 'ID');

            // 账期
            $form->select('bill_period_id', trans('payment.detail.bill_period'))
                ->options(PaymentDetail::getBillPeriodOptions())
                ->load('payment_schedule_id',
                    URL::route('select.payment_schedule.loading'),
                    'id',
                    'name'
                );

            // 付款计划
            $form->select('payment_schedule_id', trans('payment.detail.payment_schedule'))
                ->options(PaymentDetail::getPaymentScheduleOptions());

            // 供应商
            $form->select('supplier_id', trans('payment.detail.supplier'))
                ->options(PaymentDetail::getSupplierOptions());


            $form->divider();

            // 付款类型
            $form->select('pay_type', trans('payment.detail.pay_type'))
                ->options(PaymentDetail::getPayTypeOptions());

            // 付款金额
            $form->currency('money', trans('payment.detail.money'));

            // 付款时间
            $form->date('time', trans('payment.detail.time'));

            // 收款公司
            $form->text('collecting_company', trans('payment.detail.collecting_company'));

            // 备注
            $form->textarea('memo', trans('admin.memo'));

            // 收款证明
            $form->image('collecting_proof', trans('payment.detail.collecting_proof'))->nullable();
            // 付款证明
            $form->image('payment_proof', trans('payment.detail.payment_proof'))->nullable();


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
