<?php

namespace App\Admin\Controllers;

use App\Models\BillPeriod;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

/**
 * Class BillPeriodController
 *
 * 账期档案管理
 *
 * @package App\Admin\Controllers
 */
class BillPeriodController extends Controller
{
    use ModelForm;

    /**
     * 路由映射表
     *
     * - 账期总览
     * -- 账期-资金池-编辑
     * -- 账期-资金池-更新
     *
     * @var array
     */
    protected $routeMap = [
        'billGather' => 'bill.gather',
        'editCashPool' => 'bill.pool.edit',
        'updateCashPool' => 'bill.pool.update',
        'initSchedule' => 'bill.init.schedule',
    ];

    /**
     * 账期总览
     *
     * @route_name bill.gather
     * @return Content
     */
    public function index()
    {

        return Admin::content(function (Content $content) {

            $content->header(trans('bill.periods'));
            $content->description(trans('admin.list'));

            $grid = $this->grid();

//            $grid = withLayUI_Table($grid);

            $content->body($grid);
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

    public function editCashPool($id)
    {
        return Admin::content(function(Content $content) use($id){
            $content->header(trans('bill.periods'));
            $content->description('设置账期资金池');

            $content->breadcrumb(
                ['text' => '付款管理', 'href'=>''],
                ['text' => '账期汇总', 'href'=>$this->getUrl('billGather')],
                ['text' => '设置资金池', 'href'=>$this->getUrl('editCashPool')]
            );

            $form = $this->formCashPool();

            $form->setAction($this->getUrl('updateCashPool', ['id'=>$id]));

            $content->body($form->edit($id));
        });
    }

    public function updateCashPool($id)
    {
        return $this->formCashPool()->update($id);
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
     * 计划初始化界面
     *
     * @param $id
     * @return bool
     */
    public function initSchedule($id)
    {
        return  Admin::content(function(Content $content){
            $content->header(trans('bill.periods'));
            $content->description('初始化账期计划');

            $content->breadcrumb(
                ['text' => '付款管理', 'href'=>''],
                ['text' => '账期汇总', 'href'=>$this->getUrl('billGather')],
                ['text' => '初始化账期计划', 'href'=>$this->getUrl('initSchedule')]
            );

            // 展示账期的预统计信息

            // 给出可选项目，生成计划

            // **  付款类型

            // 提供清单导出 Excel

            $content->body("正在开发中");
        });
    }

    /**
     * 初始化计划内容
     *
     * @param $id
     * @return bool
     */
    public function initScheduleHandler($id)
    {
        // 生成账期的相关计划
        return "正在开发中";
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

            ### 库存金额
            $form->divider();
            // 现金余额
            $form->currency('cash_balance', trans('bill.period.cash_balance'))
                  ->prepend('￥');
            // 确认收款(已收发票总额)
            $form->currency('invoice_balance', trans('bill.period.invoice_balance'))
                 ->prepend('￥');
            // 承兑额度
            $form->currency('acceptance_line', trans('bill.period.acceptance_line'))
                 ->prepend('￥');
            // 银行发放的贷款额度
            $form->currency('loan_balance', trans('bill.period.loan_balance'))
                 ->prepend('￥');

            ## 当期发生
            $form->divider();

            // 预计收款
            $form->currency('except_balance', trans('bill.period.except_balance'))->prepend('￥');

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

    protected function formCashPool()
    {
        return Admin::form(BillPeriod::class, function (Form $form) {

            $form->tools(function(Form\Tools $tools){

                $tools->disableListButton();
            });

            $form->display('id', 'ID');

            // 状态
            $form->select('status', trans('bill.period.status'))
                ->options(BillPeriod::getStatusOptions())
                ->readOnly();

            // 账期年月
            $form->date('month', trans('bill.period.month'))
                ->format('YYYY-MM')
                ->readOnly();

            // 账期名称
            $form->text('name', trans('bill.period.name'))
                ->readOnly();

            $form->divider();

            // 账期范围
            $form->dateRange('time_begin', 'time_end', trans('bill.period.time'));

            ### 库存金额
            $form->divider();
            // 现金余额
            $form->currency('cash_balance', trans('bill.period.cash_balance'))
                ->prepend('￥');
            // 确认收款(已收发票总额)
            $form->currency('invoice_balance', trans('bill.period.invoice_balance'))
                ->prepend('￥');
            // 承兑额度
            $form->currency('acceptance_line', trans('bill.period.acceptance_line'))
                ->prepend('￥');
            // 银行发放的贷款额度
            $form->currency('loan_balance', trans('bill.period.loan_balance'))
                ->prepend('￥');

            ## 当期发生
            $form->divider();

            // 预计收款
            $form->currency('except_balance', trans('bill.period.except_balance'))->prepend('￥');

            $form->divider();


            // 负责人
            $form->text('charge_man', trans('bill.period.charge_man'))
                ->rules('required');

            $form->divider();


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $form->ignore('status');

            $form->saved(function($form){

                session()->flash('success', new MessageBag(['title'=>'更新成功！', 'message'=>'']));

                return redirect()->to($this->getUrl('billGather'));
            });

        });
    }
}
