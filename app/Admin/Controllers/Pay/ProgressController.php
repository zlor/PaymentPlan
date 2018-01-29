<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProgressController extends Controller
{
    use ModelForm;

    use ModelForm;

    protected $routeMap = [
        'index' => 'payment.schedule.progress',
        'edit' => 'progress.schedule.edit',
        'update' => 'progress.schedule.update',
        'view'  => 'progress.schedule.view'
    ];

    public function index()
    {
        return Admin::content(function(Content $content){

            $content->header(trans('progress.payment.schedule'));
            $content->description(trans('admin.list'));

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划进度', 'url'=> $this->getUrl('index')]
            );

            $content->body($this->grid());
        });
    }

    public function view($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('progress.payment.schedule'));
            $content->description(trans('admin.view'));

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划进度', 'url'=> $this->getUrl('index')],
                ['text'=>'暂停付款', 'url'=> $this->getUrl('view', ['id'=>$id])]
            );

            $content->body($this->form()->view($id));
        });
    }

    public function edit($id)
    {
        return Admin::content(function(Content $content)use($id){

            $content->header(trans('progress.payment.schedule'));
            $content->description(trans('admin.edit'));

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划进度', 'url'=> $this->getUrl('index')],
                ['text'=>'暂停调整', 'url'=> $this->getUrl('frozeEdit', ['id'=>$id])]
            );

            $content->body($this->form()->edit($id));
        });
    }

    public function update($id)
    {
        return $this->form()->update($id);
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
             */
            $grid->disableRowSelector();
            $grid->tools(function(Grid\Tools $tools){
                $tools->disableBatchActions();
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


                $action_froze_edit = _A(
                    '暂停付款',
                    ['href'=>$that->getUrl('edit', ['id'=>$paymentSchedule->id])],
                    ['title'=>'停止付款功能']
                );
                $action_progress_view = _A(
                    '查看',
                    ['href'=>$that->getUrl('view', ['id'=>$paymentSchedule->id])],
                    ['title'=>'查看计划详情']
                );

                $actionList = [];
                if($paymentSchedule->hasPayInfo())
                {
                    array_push($actionList, $action_froze_edit);

                }

                array_push($actionList, $action_progress_view);

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
                                    <li class='text-right' data-toggle='tooltip' data-title='本期计划付款'> ￥<label class='bg-white text-red'>{$plan}</label> <i class='text-gray'>金额</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='计划人'> <label class='bg-white text-defaut'>{$this->plan_man}</label> <i class='text-gray'>担当</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='计划时间'> <label class='bg-white text-warning'>{$this->plan_time}</label> <i class='text-gray'>时间</i></li>
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
                                    <li class='text-right' data-toggle='tooltip' data-title='本期一次核定付款'> ￥<label class='bg-white text-red'>{$audit}</label> <i>金额</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='一次核定人'> <label class='bg-white text-defaut'>{$this->audit_man}</label> <i>担当</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='一次核定时间'> <label class='bg-white text-warning'>{$this->audit_time}</label> <i>时间</i></li>
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
                                    <li class='text-right' data-toggle='tooltip' data-title='本期二次核定应付付款'> ￥<label class='bg-white text-red'>{$final}</label> <i class='text-gray'>金额</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='二次核定人'> <label class='bg-white text-default'>{$this->final_man}</label> <i class='text-gray'>担当</i></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='二次核定时间'> <label class='bg-white text-warning'>{$this->final_time}</label> <i class='text-gray'>时间</i></li>
                                </ul>
                            </div>";
                    }

                    return $html;
                });

            $grid->column('due_money', trans('payment.schedule.due_money'))
                ->display(function($value){

                    $html = '';
                    if($this->hasPayInfo())
                    {
                        $due_money  = number_format($value, 2);

                        $html = "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right text-green' data-toggle='tooltip' data-title='最终应付款'> 
                                        <div>￥<label class='bg-white'>{$due_money}</label> <i class='text-gray'>金额</i></div>
                                    </li>
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
        });
    }

    // protected function form()
    // {
    //     return Admin::form(PaymentSchedule::class, function(Form $from){
    //
    //         $from->radio('is_froze', trans(''))
    //              ->options(PaymentSchedule::getBooleanOptions('payment.schedule', 'is_froze'));
    //         $from->textarea('froze_memo', trans('admin.memo'));
    //     });
    // }

    /**
     * 付款核定信息
     * @return Form
     */
    protected function form()
    {
        return Admin::form(PaymentSchedule::class, function (Form $form) {

            $form->row(function (Form\Row $row)use($form){
                $row->width(12)
                    ->radio('is_froze', trans('payment.schedule.is_froze'))
                    ->options(PaymentSchedule::getBooleanOptions('payment.schedule', 'is_froze'));
                $row->width(12)
                    ->textarea('froze_memo', trans('admin.memo'));
                $row->divide();
            });

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
                    ->readonly();
                $row->width(0)
                    ->hidden('status')
                    ->readonly();
            });

            $form->ignore(['bill_period_id','supplier_id', 'payment_type_id', 'payment_materiel_id']);

            $form->saving(function(Form $form){
                if($form->is_froze)
                {
                    $form->status = PaymentSchedule::STATUS_FROZE;
                }else{
                    $form->status = PaymentSchedule::STATUS_PAY;
                }
            });
        });
    }
}
