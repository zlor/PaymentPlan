<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use App\Models\UserEnv;
use Carbon\Carbon;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
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
        'index' => 'payment.schedule.audit',

        'auditEdit' => 'audit.schedule.edit',
        'finalEdit' => 'final.schedule.edit',
        'lockEdit' => 'lock.schedule.edit',

    ];

    public function index(Request $request)
    {
        return Admin::content(function(Content $content){

            $content->header(trans('audit.payment.schedule'));
            $content->description(trans('admin.list'));

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('index')]
            );

            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('audit.payment.schedule'));
            $content->description('初稿核定');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('index')],
                ['text'=>'初稿核定', 'url'=> $this->getUrl('auditEdit', ['id'=>$id])]
            );

            $form = $this->formAudit()->edit($id);

            $content->body($form);
        });
    }

    public function update($id)
    {
        return $this->formAudit()->update($id);
    }

    public function finalEdit($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('audit.payment.schedule'));
            $content->description('终稿核定');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('index')],
                ['text'=>'终稿核定', 'url'=> $this->getUrl('finalEdit', ['id'=>$id])]
            );

            $form = $this->formFinal()->edit($id);

            $content->body($form);
        });
    }

    public function finalUpdate($id)
    {
        return $this->formFinal()->update($id);
    }

    public function lockEdit($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('audit.payment.schedule'));
            $content->description('付款核定');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划审核', 'url'=> $this->getUrl('index')],
                ['text'=>'付款核定', 'url'=> $this->getUrl('lockEdit', ['id'=>$id])]
            );

            $form = $this->formLock()->edit($id);

            $content->body($form);
        });
    }

    public function lockUpdate($id)
    {
        return $this->formLock()->update($id);
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
            });

            /**
             * 操作行
             *
             * 初始化状态的允许审核记录
             */
            $that = $this;

            $grid->actions(function(Grid\Displayers\Actions $actions)use($that){

                $paymentSchedule = $this->row;

                $actions->disableEdit();
                $actions->disableDelete();


                $action_audit_edit = _A(
                    '核定',
                    ['href'=>$that->getUrl('auditEdit', ['id'=>$paymentSchedule->id])],
                    ['title'=>'初稿核定']
                );
                $action_final_edit = _A(
                    '终核',
                    ['href'=>$that->getUrl('finalEdit', ['id'=>$paymentSchedule->id])],
                    ['title'=>'终稿核定']
                );
                $action_lock_edit  = _A(
                    '应付款敲定',
                    ['href'=>$that->getUrl('lockEdit',  ['id'=>$paymentSchedule->id])],
                    ['title'=>'应付款敲定']
                );

                $actionList = [];
                if($paymentSchedule->hasPayInfo())
                {
                    array_push($actionList, $action_lock_edit);

                }else if($paymentSchedule->hasLockInfo())
                {
                    array_push($actionList, $action_lock_edit);
                }
                else if($paymentSchedule->hasFinalInfo())
                {
                    array_push($actionList, $action_lock_edit);
                    array_push($actionList, $action_final_edit);

                }else if($paymentSchedule->hasAuditInfo()){

                    array_push($actionList, $action_final_edit);
                    array_push($actionList, $action_audit_edit);

                }else{
                    array_push($actionList, $action_audit_edit);
                }

                $actions->append(join("&nbsp;&nbsp;", array_reverse($actionList)));

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
            $grid->column('supplierBalanceInfo', trans('payment.schedule.supplierBalanceInfo'))
                ->display(function(){
                    $total = number_format($this->supplier_balance, 2);
                    $last  = number_format($this->supplier_lpu_balance, 2);
                    $money  = number_format($this->due_money, 2);
                    return "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='总应付款'> ￥<label class='bg-white text-danger'>{$total}</label> <i>总</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='上期未付清余额'> ￥<label class='bg-white text-warning'>{$last}</label> <i>余</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期应付款'> ￥<label class='bg-white text-red'>{$money}</label> <i>本</i></li>
                                </ul>
                            </div>";
                });

            // // 供应商余额(总应付款)
            // $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'))
            //     ->currency();

            // 计划相关信息
            $grid->column('planInfo', trans('payment.schedule.planInfo'))
                ->display(function($value){
                    $plan  = number_format($this->plan_due_money, 2);
                    return "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期计划付款'> ￥<label class='bg-white text-red'>{$plan}</label> <i>金额</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='计划人'> <label class='bg-white text-defaut'>{$this->plan_man}</label> <i>担当</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='计划时间'> <label class='bg-white text-warning'>{$this->plan_time}</label> <i>时间</i></li>
                                </ul>
                            </div>";
                });

            // 核定相关信息
            $grid->column('auditInfo', trans('payment.schedule.auditInfo'))
                ->display(function($value){

                    $html = '';

                    if($this->hasAuditInfo())
                    {
                        $audit = number_format($this->audit_due_money, 2);
                        $html  =  "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期核定付款'> ￥<label class='bg-white text-red'>{$audit}</label> <i>金额</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='核定人'> <label class='bg-white text-defaut'>{$this->audit_man}</label> <i>担当</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='核定时间'> <label class='bg-white text-warning'>{$this->audit_time}</label> <i>时间</i></li>
                                </ul>
                            </div>";
                    }
                    return $html;
                });

            // 终核相关信息
            $grid->column('finalInfo', trans('payment.schedule.finalInfo'))
                ->display(function($value){

                    $html = '';

                    if($this->hasFinalInfo())
                    {
                        $final  = number_format($this->final_due_money, 2);
                        $html= "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期终核付款'> ￥<label class='bg-white text-red'>{$final}</label> <i>金额</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='终核人'> <label class='bg-white text-defaut'>{$this->final_man}</label> <i>担当</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='终核时间'> <label class='bg-white text-warning'>{$this->final_time}</label> <i>时间</i></li>
                                </ul>
                            </div>";
                    }

                    return $html;
                });

            $grid->column('payInfo', trans('payment.schedule.payInfo'))
                ->display(function($value){

                    $html = '';
                    if($this->hasPayInfo())
                    {
                        $paid  = number_format($this->paid_money, 2);
                        $cash_paid = number_format($this->cash_paid, 2);
                        $acceptance_paid = number_format($this->acceptance_paid, 2);

                        $html = "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期已付款'> ￥<label class='bg-white text-red'>{$paid}</label> <i>总额</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='已付现金'> <label class='bg-white text-defaut'>{$cash_paid}</label> <i>现金</i></li>
                                    <li class='text-right' data-toggle='tooltip' data-title='已付承兑'> <label class='bg-white text-warning'>{$acceptance_paid}</label> <i>承兑</i></li>
                                </ul>
                            </div>";
                    }

                    return $html;
                });
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
     * 初稿核定信息
     */
    protected function formAudit()
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

                $row->width(12)->divide();

                $row->width(6)
                    ->text('plan_man', trans('payment.schedule.plan_man'))
                    ->readonly();

                $row->width(3)
                    ->text('plan_time', trans('payment.schedule.plan_time'))
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

            // 核定调整
            $form->row(function (Form\Row $row) use ($form)
            {

                $row->width(12)->divide();
                $row->width(6)
                    ->text('audit_man', trans('payment.schedule.audit_man'))
                    ->rules('nullable');
                $row->width(3)
                    ->date('audit_time', trans('payment.schedule.audit_time'))
                    ->format('YYYY-MM-DD')
                    ->default(Carbon::now());
                $row->width(3)
                    ->currency('audit_due_money', trans('payment.schedule.audit_due_money'))
                    ->prepend('￥')
                    ->rules('required');
                $row->width(12)
                    ->textarea('memo_audit', trans('admin.memo'))
                    ->rules('nullable');

                $row->width(0)
                    ->hidden('status');
            });

            $form->saving(function(Form $form){
                if(empty($form->audit_man))
                {
                    $form->audit_man =  Admin::user()->name;
                }

                $form->status = PaymentSchedule::STATUS_CHECK_AUDIT;
            });

            $form->ignore(['bill_period_id','supplier_id', 'payment_type_id', 'payment_materiel_id']);
        });
    }

    /**
     * 终稿核定信息
     * @return Form
     */
    protected function formFinal()
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
                    ->setWidth('100%')
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

    /**
     * 付款核定信息
     * @return Form
     */
    protected function formLock()
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
                    ->setWidth('100%')
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
                    ->readonly();
                $row->width(3)
                    ->date('final_time', trans('payment.schedule.final_time'))
                    ->format('YYYY-MM-DD')
                    ->readonly();
                $row->width(3)
                    ->currency('final_due_money', trans('payment.schedule.final_due_money'))
                    ->prepend('￥')
                    ->readonly();
                $row->width(12)
                    ->textarea('memo_final', trans('admin.memo'))
                    ->readonly();


            });

            // 终稿核定调整
            $form->row(function (Form\Row $row) use ($form)
            {
                $row->width(12)
                    ->currency('due_money', trans('payment.schedule.due_money'))
                    ->prepend('￥')
                    ->rules('required');
                $row->width(0)
                    ->hidden('status');
            });

            $form->ignore(['bill_period_id','supplier_id', 'payment_type_id', 'payment_materiel_id']);

            $form->saving(function(Form $form){
                $form->status = PaymentSchedule::STATUS_PAY;
            });
        });
    }
}
