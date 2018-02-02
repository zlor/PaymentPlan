<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index' => 'payment.schedule.batch',
        'post'  => 'batch.schedule.store',
    ];

    /**
     * 批量处理数据表-模板
     *
     * @param Request $request
     *
     * @return Content
     */
    public function index(Request $request)
    {
        return Admin::content(function(Content $content){

            ## 固定表头
            $css = <<<STYLE
<style>
td{
}
td>div>ul.list-unstyled>li.show-info>div{
    display:inline-flex;
}
td>div>ul.list-unstyled>li.edit-info{
    float:right;
}
td>div>ul.list-unstyled>li.edit-info>div input{
    width:6.8em;
}
td>div>ul.list-unstyled>li i.text-gray{
    display:none;
}
</style>
STYLE;
            $content->row($css);

            Admin::js(asset('/vendor/laravel-admin/ele-fixed/eleFixed.js'));

            $script =<<<SCRIPT
        $(function(){
            var filterHeight = 29;
            
            var initFixed = function(){
                var headHeight = $('.ele-fixed').offset().top;
                eleFixed.push({
                    target: document.getElementsByClassName('ele-fixed')[0], // it must be a HTMLElement
                    offsetTop: (headHeight - filterHeight + 30) // height from window offsetTop
                });
                eleFixed.push({
                    target: document.getElementsByClassName('counter')[0], // it must be a HTMLElement
                    offsetTop: (headHeight + 4 - filterHeight + 30) // height from window offsetTop
                });
                
                var height = $('#filter_div').height();
                filterHeight = height;
            };
            $('#filter_btn').click(function(){
                eleFixed.delete(document.getElementsByClassName('ele-fixed')[0]);
                eleFixed.delete(document.getElementsByClassName('counter')[0]);
                initFixed();
            });
            initFixed();
            
        });
        
SCRIPT;
            Admin::script($script);

            $content->body($this->_effectBatchGrid());
        });
    }

    /**
     * 处理批量操作
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * 批量处理通用改造
     */
    protected function _effectBatchGrid()
    {
        $grid = $this->grid();

        //不使用分页
        $grid->disablePagination();

        return $grid;
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
             * 操作行
             *
             * 初始化状态的允许审核记录
             */
            $that = $this;

            $grid->footer(function(Grid\Tools\Footer $footer)use($grid){
                $rows = $grid->rows();

                $count = count($rows);

                $map = [
                    'supplier_balance',
                    'supplier_lpu_balance',
                    'plan_due_money',
                    'audit_due_money',
                    'final_due_money',
                    'due_money',
                    'cash_paid',
                    'acceptance_paid',
                ];

                $row = [];
                $sum = [];

                foreach ($map as $item)
                {
                    $row[$item] = $count>0?$footer->column($item):(new Collection());

                    $sum[$item] = number_format($row[$item]->sum(), 2);
                }
                $sum['paid_money'] = number_format($row['cash_paid']->sum() + $row['acceptance_paid']->sum(), 2);

                $footer->td("共 <span>{$count}</span> 条")
                    ->td()->td()
                    ->td("<div><ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='总应付款总计'> <div>￥<label class='bg-white text-danger'>{$sum['supplier_balance']}</label> <i>总</i></div></li>
                                    <!--<li class='text-right text-gray' data-toggle='tooltip' data-title='上期未付清余额总计'> ￥<label class='bg-white text-gray'>{$sum['supplier_balance']}</label> <i>余</i></li>-->
                                </ul>
                            </div>")
                    ->td("<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期计划应付'> <div>￥<label class='bg-white text-red'>{$sum['plan_due_money']}</label> <i class='text-gray'>金额</i></div></li>
                                </ul>
                            </div>")
                    ->td("<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期一核应付'> <div>￥<label class='bg-white text-red'>{$sum['audit_due_money']}</label> <i class='text-gray'>金额</i></div></li>
                                </ul>
                            </div>")
                    ->td("<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期二核应付'> <div>￥<label class='bg-white text-red'>{$sum['final_due_money']}</label> <i class='text-gray'>金额</i></div></li>
                                </ul>
                            </div>")
                    ->td("<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='敲定都应付'> <div>￥<label class='bg-white text-green'>{$sum['due_money']}</label> <i class='text-gray'>金额</i></div></li>
                                </ul>
                            </div>")
                    ->td("<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='现金({$sum['cash_paid']}), 承兑({$sum['acceptance_paid']})'> <div>￥<label class='bg-white text-red'>{$sum['paid_money']}</label> <i>总额</i></div></li>
                                </ul>
                            </div>");
            });

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.bill_period'))
                ->display(function($value){
                    $title_billPeriodName = trans('payment.schedule.bill_period');
                    $title_typeName = trans('payment.schedule.payment_type');
                    return "<div>
                                <label class=' badge-default' title='{$title_billPeriodName}'>{$this->bill_period['name']}</label><br>
                                <label class=' label-' title='{$title_typeName}'>{$this->payment_type['name']}</label>
                            </div>";
                });
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
                                    <li class='text-right' data-toggle='tooltip' data-title='总应付款'> <div>￥<label class='bg-white text-danger'>{$total}</label> <i>总</i></div></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='上期未付清余额'> <div>￥<label class='bg-white text-gray'>{$last}</label> <i>余</i></div></li>
                                    <!-- <li class='text-right text-green' data-toggle='tooltip' data-title='本期应付款'> ￥<label class='bg-white text-green'>{$money}</label> <i class='text-black'>本</i></li>-->
                                </ul>
                            </div>";
                });

            // 计划相关信息
            $grid->column('planInfo', trans('payment.schedule.planInfo'))
                ->display(function($value){
                    $plan  = number_format($this->plan_due_money, 2);
                    return "<div class='planArea'>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='show-info text-right' data-toggle='tooltip' data-title='本期计划应付'> <div>￥<label class='bg-white text-red'>{$plan}</label> <i class='text-gray'>金额</i></div></li>
                                    <li class='show-info text-right text-gray' data-toggle='tooltip' data-title='担当({$this->plan_man}),时间[{$this->plan_time}]'> <div><label class='bg-white text-gray'>{$this->plan_time}</label> <i class='text-gray'>时间</i></div></li>
                                    <li class='edit-info hide' data-change='' data-origin='{$this->plan_due_money}'></li>
                                    <li class='edit-message-info hide'></li>
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
                        $html  =  "<div class='auditArea'>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期一核应付'> <div>￥<label class='bg-white text-red'>{$audit}</label> <i class='text-gray'>金额</i></div></li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='担当({$this->audit_man}),时间({$this->audit_time})'> <div><label class='bg-white text-gray'>{$this->audit_time}</label> <i class='text-gray'>时间</i></div></li>
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
                        $html= "<div class='finalArea'>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期二核应付'> 
                                        <div>￥<label class='bg-white text-red'>{$final}</label> <i class='text-gray'>金额</i></div>
                                    </li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='担当人({$this->final_man}),时间({$this->final_time})'> 
                                        <div><label class='bg-white text-gray'>{$this->final_time}</label> <i class='text-gray'>时间</i></div>
                                    </li>
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

                        $html = "<div class='lockArea'>
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

                        $cashPercent = round((intval(100* $this->paid_money)==0)?0:(100*$this->cash_paid/$this->paid_money), 2);

                        $html = "<div>
                                <ul class='list-unstyled' style='margin: auto'>
                                    <li class='text-right' data-toggle='tooltip' data-title='本期已付款'> 
                                        <div>￥<label class='bg-white text-red'>{$paid}</label> <i>总额</i></div>
                                    </li>
                                    <li class='text-right text-gray' data-toggle='tooltip' data-title='现金({$cash_paid}), 承兑({$acceptance_paid})'> 
                                        <div><label class='bg-white text-default'>{$cashPercent}%</label> <i>现金</i></div>
                                    </li>
                                </ul>
                            </div>";
                    }

                    return $html;
                });

            // 状态
            $grid->column('status', trans('payment.schedule.status'))
                ->display(function($value){
                    return trans('payment.schedule.status.' . $value);
                });
        });
    }

}
