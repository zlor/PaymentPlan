<?php

namespace App\Admin\Controllers;

use App\Models\BillPeriod;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class BillPeriodController extends Controller
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

            $content->header(trans('bill.periods'));
            $content->description(trans('admin.list'));

            $content->body($this->grid());
        });
    }

    /**
     * 显示账期详情
     * @param $id
     *
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header(trans('bill.periods'));
            $content->description(trans('admin.show'));

            $content->body($this->form()->view($id));
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

            $content->header(trans('bill.periods'));
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

            $content->header(trans('bill.periods'));
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
        return Admin::grid(BillPeriod::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            // 不需要加载多行操作
            $grid->disableRowSelector();
            // 不需要加载导出按钮
            $grid->disableExport();

            // 账期名称
            $grid->column('name', trans('bill.period.name'));

            // 账期月份（年月）
            $grid->column('month', trans('bill.period.month'));

            // 状态
            $grid->column('status', trans('bill.period.status'))->display(function($value){
                return trans('bill.period.status.'.$value);
            });

            // 负责人
            $grid->column('charge_man', trans('bill.period.charge_man'));


            // 现金池
            $grid->column('cash_pool', trans('bill.period.cash_pool'))->display(function($value){
                return $this->cash_pool;
            });

            // 账期余额
            $grid->column('balance', trans('bill.period.balance'))->display(function($value){
                return $this->balance;
            });


            $grid->created_at();
            $grid->updated_at();


            $grid->filter(function(Grid\Filter $filter){

                $filter->like('charge_man', trans('bill.period.charge_man'));

            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(BillPeriod::class, function (Form $form) {

            $form->display('id', 'ID');

            // 账期年月
            $form->date('month', trans('bill.period.month'))
                ->format('YYYY-MM')
                ->default(date('Y-m'))
                ->rules('required');

            // 账期名称
            $form->text('name', trans('bill.period.name'))
                ->default(date('Y年m月'))
                ->rules('required');

            // 账期范围
            $form->dateRange('time_begin', 'time_end', trans('bill.period.time'));

            // 现金余额
            $form->currency('cash_balance', trans('bill.period.cash_balance'))->prepend('￥');

            // 确认收款(已收发票总额)
            $form->currency('invoice_balance', trans('bill.period.invoice_balance'))->prepend('￥');

            // 预计收款
            $form->currency('except_balance', trans('bill.period.except_balance'))->prepend('￥');

            // 承兑额度
            $form->currency('acceptance_line', trans('bill.period.acceptance_line'))->prepend('￥');

            $form->divider();

            // 状态
            $form->select('status', trans('bill.period.status'))
                ->options(BillPeriod::getStatusOptions())
                ->default('standby');

            // 负责人
            $form->text('charge_man', trans('bill.period.charge_man'))
                ->rules('required');

            $form->divider();

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

        });
    }
}
