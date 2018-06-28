<?php

namespace App\Admin\Controllers;

use App\Models\BillPeriod;

use App\Models\PaymentSchedule;
use App\Models\PaymentType;
use App\Models\Supplier;
use App\Models\SupplierInvoiceGather;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Response;
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
        'refreshInvoiceGather' =>'base.supplier.invoice.gather.refresh',
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
     * @return Content
     */
    public function initSchedule($id)
    {
        $billPeriod = BillPeriod::query()->find($id);

        return Admin::content(function(Content $content)use($billPeriod){

            $content->header(trans('bill.periods'));
            $content->description('初始化账期计划');

            $content->breadcrumb(
                ['text' => '付款管理', 'href'=>''],
                ['text' => '账期汇总', 'href'=>$this->getUrl('billGather')],
                ['text' => '初始化账期计划', 'href'=>$this->getUrl('initSchedule')]
            );

            // 展示账期信息
            // -展示付款计划类型
            // -- 补充完整信息不完善的供应商
            $paymentTypeBox = new Box('当前账期: '.$billPeriod->name." - [{$billPeriod->month}]", $this->_paymentTypesPart($billPeriod));
            $paymentTypeBox->collapsable()->solid();
            $content->row($paymentTypeBox);

            // 展示供应商信息
            $supplierBox = new Box('供应商信息', $this->_supplierPart($billPeriod));
            $supplierBox->collapsable()->solid();
            $content->row($supplierBox);

            // 给出可选项目，生成计划
            // **  付款类型
            // 提供清单导出 Excel
            $handlerBox =  new Box('生成计划信息', $this->_schedulePart($billPeriod));
            $content->row($handlerBox);
        });
    }

    /**
     * 选择要处理的付款类型
     * || 默认全部选中
     *
     */
    private function _paymentTypesPart($billPeriod)
    {
        $payment_types = PaymentType::all();

        $yearMonth = $billPeriod->month;

        $year = date('Y', strtotime($yearMonth));
        $month = date('m', strtotime($yearMonth));

        $url = $this->getUrl('refreshInvoiceGather');

        return view("admin.schedule.init_chosse_payment_types",
            compact('payment_types', 'month', 'billPeriod', 'url'));
    }

    /**
     * 罗列供应商信息
     * @return string
     */
    private function _supplierPart($billPeriod)
    {
        if(empty($billPeriod) || empty($billPeriod->id))
        {
            return "";
        }
        // 获取账期关联的供应商

        // 账期所在月份
        $yearMonth = $billPeriod->month;
        $year = date('Y', strtotime($yearMonth));
        $month = date('m', strtotime($yearMonth));

        // 符合条件的供应商
        $suppliers = Supplier::all();
        $balanceSuppliers = [];
        $invoiceGathers = [];

        foreach ($suppliers as $supplier)
        {
            $balanceSuppliers[$supplier->id] =  $supplier->getBalanceMoneyMonth($year, $month);
            $invoiceGathers[$supplier->id] = $supplier->getInvoiceGatherMonth($year, $month);
        }

        if(empty($suppliers))
        {
            return '无需要付款的供应商';
        }else{
            return view('admin.schedule.init_suppliers', compact('month','suppliers', 'balanceSuppliers', 'invoiceGathers'));
        }
    }

    public function _schedulePart($billPeriod)
    {
        $url = $this->getUrl('initSchedule', ['id'=>$billPeriod->id]);
        return view('admin.schedule.init_schedules', [
            'url' => $url,
            'billPeriod' => $billPeriod
        ]);
    }

    /**
     * 初始化计划内容
     *
     * @param $id
     * @return string
     */
    public function initScheduleHandler($id)
    {
        /**
         * @var BillPeriod $billPeriod
         */
        $billPeriod =  BillPeriod::query()->find($id);

        if(empty($billPeriod))
        {
            return Response::json(['status'=>false, 'message'=>'获取账期信息失败']);
        }
        // 获取账期信息
        $yearMonth = $billPeriod->month;
        $year = date('Y', strtotime($yearMonth));
        $month = date('m', strtotime($yearMonth));

        // 遍历供应商信息
        $suppliers = Supplier::all();
        // 构建
        $scheduleList = [];
        $failSupplierList = [];
        $count = 0;
        $columns = $billPeriod->getMonthNumber(true);
        $date = date('Y-m-d ');
        foreach ($suppliers as $supplier)
        {
            // 获取供应商账户余额
            $balanceMoney = $supplier->getBalanceMoneyMonth($year, $month);

            $rowWhere = [
                'supplier_id' => $supplier->id,
                'bill_period_id' => $billPeriod->id,
            ];
            $months_pay_cycle = empty($billPeriod->months_pay_cycle)?3:$billPeriod->months_pay_cycle;
            $row = [
                'bill_period_id' => $billPeriod->id,
                'supplier_id' => $supplier->id,
                'name' => $supplier->code,
                'supplier_name' =>$supplier->name,
                'supplier_balance' =>$balanceMoney,
                'payment_type_id' =>$supplier->payment_type_id,
                'payment_materiel_id' =>$supplier->payment_materiel_id,
                'materiel_name' => empty($supplier->payment_materiel)?'':$supplier->payment_materiel->name,
                'charge_man' => empty($supplier->charge_man)?'':$supplier->charge_man,
                'batch' => 0,
                'status' =>'init',
                // 付款周期
                'pay_cycle' => $months_pay_cycle . '个月',
                // 截止到的月份数
                'pay_cycle_month'=> $billPeriod->getCycleMonthByNum($months_pay_cycle),
                'memo' => '初始化',
            ];

            // 获取供应商关联月份的发票数额
            foreach ($columns as $column)
            {
                $subMonth =intval(substr($column, strlen('invoice_m_')));
                $subYear = ($subMonth>$month)?($year-1):$year;

                $row[$column] =  $supplier->supplier_invoice_gathers->where('year', $subYear)->where('month', $subMonth)->sum('money');
            }

            /**
            * 推算建议应付款数额
            */
            $row['suggest_due_money'] = $billPeriod->guestSuggestDueMoney($row, $row['pay_cycle_month']);

            // 构建信息
            $newSchedule = PaymentSchedule::updateOrCreate($rowWhere, $row);

            if($newSchedule)
            {
                $count++;
                $scheduleList[] = $newSchedule;
            }else{
                $failSupplierList[] = $supplier;
            }
        }

        return Response::json(['status'=>true, 'message'=>"构建/更新：计划数{$count}"]);
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
