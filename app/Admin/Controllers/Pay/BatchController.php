<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GetSpanMoney;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class BatchController extends Controller
{
    use ModelForm;

    use GetSpanMoney;

    protected $routeMap = [
        'index' => 'payment.schedule.batch',
        'post'  => 'batch.schedule.store',

    ];

    protected $batch_column = '';

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
.table th,.table td{
    white-space: nowrap;
    border:1px solid #efefef;
    padding:2px!important;
}

.ul-area .action{
    display:none;
}

span.edit .ul-area .info{
    display:none;
}
span.edit .ul-area .action{
    display:block;
}


td>div>ul.list-unstyled>li.show-info>div{
    
    white-space: nowrap;
}
td>span>ul.list-unstyled>li.edit-info{
    float:right;
}
td>span>ul.list-unstyled>li.edit-info>div input{
    width:6.8em;
}
td ul.list-unstyled>li i.text-gray{
    display:none;
}
td>span>ul.list-unstyled>li.text-gray{
    display:none;
}
td .text-money{
    color:black;
}
td .text-money.text-money-minius{
    color:red;
}
td .text-money.text-money-zero{
    color:gray;
}
td .coin{
    color:#bfbfbf;
    color:white;
    margin-right:.3em;
}
</style>
STYLE;
            $content->row($css);

            Admin::js(asset('/vendor/laravel-admin/ele-fixed/eleFixed.js'));

            $script =<<<SCRIPT
    var filterHeight = $('#filter_div').height();
    var headHeight = $('.ele-fixed').offset().top;
    var offset = 0;
    
    var initFixed = function(){
        eleFixed.push({
            target: document.getElementsByClassName('ele-fixed')[0], // it must be a HTMLElement
            offsetTop: (headHeight + offset) // height from window offsetTop
        });
        eleFixed.push({
            target: document.getElementsByClassName('counter')[0], // it must be a HTMLElement
            offsetTop: (headHeight + 4 + offset) // height from window offsetTop
        });
        
    };
    $('#filter_btn').click(function(){
        
        eleFixed.delete(document.getElementsByClassName('ele-fixed')[0]);
        eleFixed.delete(document.getElementsByClassName('counter')[0]);
        
        if( $('.custom-filter').hasClass('collapsed-box'))
        {
            offset = 0;
        }else{
            offset = -filterHeight + 29.5;
        }
        initFixed();
    });
    initFixed();
        
SCRIPT;
            Admin::script($script);

            $content->body($this->_effectBatchGrid());

            config(['admin.layout'=>['sidebar-collapse']]);
        });
    }


    /**
     * 更新字段
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id = 0)
    {
        $data = [
            'success' => false,
            'msg'     => '获取失败',
            'money' => 0,
            'money_format' => '',
        ];
        // 更新数据
        $paymentSchedule = PaymentSchedule::query()->find($id);

        if(empty($paymentSchedule))
        {
            $data['msg'] = '未找到对应记录,更新失败';
            return  response()->json($data);
        }

        $field = $this->batch_column;

        if(!in_array($field, ['plan_due_money', 'audit_due_money', 'final_due_money', 'due_money']))
        {
            $data['msg'] = "模式错误,错误编码[{$field}]";
            return  response()->json($data);
        }

        $params =  Input::get();

        $res = $paymentSchedule->updateByBatch($field, $params);


        if($res['success'])
        {
            $data['success'] = true;
            $data['msg'] = '更新成功';
            $data['money'] = $paymentSchedule->$field;
            $data['money_format'] = number_format($paymentSchedule->$field, 2);
        }else{
            $data['msg'] = $res['msg'];
        }

        return  response()->json($data);

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

            // $grid->id('ID')->sortable();

            /**
             * 暂不提供的按钮
             *
             * 创建、导出
             */
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();

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

                    $sum[$item] = $row[$item]->sum();
                }
                $sum['paid_money'] = $row['cash_paid']->sum() + $row['acceptance_paid']->sum();

                $footer->td("共 <span>{$count}</span> 条", 12)
                    ->td( $this->_getMoneySpan($sum['supplier_lpu_balance'], ['title'=>'上期未付清']) )
                    ->td( $this->_getMoneySpan($sum['supplier_balance'], ['title'=>'总应付款总计']) )
                    ->td( $this->_getMoneySpan($sum['plan_due_money'], ['title'=>'本期计划应付', 'spanClass'=>'planHead']) )
                    ->td( $this->_getMoneySpan($sum['audit_due_money'], ['title'=>'本期一核应付', 'spanClass'=>'auditHead']) )
                    ->td( $this->_getMoneySpan($sum['final_due_money'], ['title'=>'本期二核应付', 'spanClass'=>'finalHead']) )
                    ->td( $this->_getMoneySpan($sum['final_due_money'], ['title'=>'敲定都应付', 'spanClass'=>'dueHead']) )
                    ->td( $this->_getMoneySpan($sum['paid_money'], ['title'=>"现金({$sum['cash_paid']}), 承兑({$sum['acceptance_paid']})"]));
            });

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.payment_type'))
                ->display(function($value){
                    $title_billPeriodName = trans('payment.schedule.bill_period');
                    $title_typeName = trans('payment.schedule.payment_type');
                    return "<span>
                                <label class=' label-' title='{$title_typeName}'>{$this->payment_type['name']}</label>
                            </span>";
                    //<label class=' badge-default' title='{$title_billPeriodName}'>{$this->bill_period['name']}</label>
                });

            // 供应商
            $grid->column('supplier_name', trans('payment.schedule.supplier_name'))
                ->display(function($value){
                    $txt = mb_strlen($value)>10?(mb_substr($value, 0, 10).'...'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$txt}</span>";
                });
            // 科目
            $grid->column('name', trans('payment.schedule.name'));
            // 物料名称
            $grid->column('materiel_name', trans('payment.schedule.payment_materiel'))
                ->display(function($value){
                    $txt =  mb_strlen($value)>4?(mb_substr($value, 0, 4).'..'):$value;
                   return "<span data-toggle='tooltip' data-title='{$value}'>{$txt}</span>";
                });
            // 付款确认人
            $grid->column('charge_man', trans('payment.schedule.charge_man'));

            // 付款周期
            $grid->column('pay_cycle', trans('payment.schedule.pay_cycle'))
                ->display(function($value){
                    $txt =  mb_strlen($value)>5?(mb_substr($value, 0, 5).'..'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$txt}</span>";
                });

            $that = $this;

            // 应付款发票
            // 按照当前的账期月份进行排序
            $defaultMonthMap = $defaultBillPeriod->getMonthNumber(true);
            foreach ($defaultMonthMap as $item)
            {
                $grid->column($item, trans('payment.schedule.'.$item))
                     ->display(function($value)use($that,$item){
                         return $that->_getMoneySpan($value, ['title'=>trans('payment.schedule.'.$item).'发票', 'noCoin'=>true]);
                     });
            }

            // 客户初期应付
            $grid->column('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'))
                ->display(function()use($that){
                    return $that->_getMoneySpan($this->supplier_lpu_balance, ['title'=>'上期未结清']);
                });

            // 应付金额
            $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'))
                ->display(function()use($that){
                    return $that->_getMoneySpan(
                        $this->supplier_balance,
                        [
                            // 'title'=>'总应付款'
                        ]
                    );
                });

            // 计划相关信息
            $grid->column('planInfo', trans('payment.schedule.planInfo'))
                ->display(function()use($that){


                    return $that->_getMoneySpan(

                        $this->plan_due_money,
                        [
                            'spanClass'=>'planArea',
                            'liClass'=>'plan_money',
                            // 'title'=>"担当({$this->plan_man}),时间[{$this->plan_time}]",
                            'title'=>"{$this->plan_man}:{$this->plan_time}",
                            'action' => 'open',
                            'url' => $that->getUrl('editPlan', ['id'=>$this->getKey()], '', true),
                        ]
                    );
                });


            // 核定相关信息
            $grid->column('auditInfo', trans('payment.schedule.auditInfo'))
                ->display(function($value)use($that){

                    $html = $that->_getMoneySpan(
                        $this->audit_due_money,
                        [
                            'spanClass'=>'auditArea',
                            'liClass'=>'audit_money',
                            // 'title'=>"担当({$this->audit_man}),时间({$this->audit_time})",
                            'title'=>"{$this->audit_man}:{$this->audit_time}",
                            'action' => 'open',
                            'url' => $that->getUrl('editAudit', ['id'=>$this->getKey()], '', true),
                        ]
                    );

                    return $html;
                });

            // 终核相关信息
            $grid->column('finalInfo', trans('payment.schedule.finalInfo'))
                ->display(function($value)use($that){

                    $html = $that->_getMoneySpan(
                        $this->final_due_money,
                        [
                            'spanClass'=>'finalArea',
                            'liClass'=>'final_money',
                            // 'title'=>"担当人({$this->final_man}),时间({$this->final_time})",
                            'title'=>"{$this->final_man}:{$this->final_time}",
                            'action' => 'open',
                            'url' => $that->getUrl('editFinal', ['id'=>$this->getKey()], '', true),
                        ]
                    );

                    return $html;
                });

            $grid->column('due_money', trans('payment.schedule.due_money'))
                ->display(function($value)use($that){

                    $html = $that->_getMoneySpan(
                        $value,
                        [
                            'spanClass'=>'dueArea',
                            'liClass'=>'due_money',
                            // 'title'=>"最终应付款",
                            'action' => 'open',
                            'url' => $that->getUrl('editDue', ['id'=>$this->getKey()], '', true),
                        ]
                    );

                    return $html;
                });

            $grid->column('payInfo', trans('payment.schedule.payInfo'))
                ->display(function($value)use($that){

                    $html = '';
                    if($this->hasPayInfo())
                    {
                        $cash_paid = number_format($this->cash_paid, 2);
                        $acceptance_paid = number_format($this->acceptance_paid, 2);

                        $cashPercent = round((intval(100* $this->paid_money)==0)?0:(100*$this->cash_paid/$this->paid_money), 2);

                        $html = $that->_getMoneySpan($this->paid_money, ['title'=>"本期已付款,现金{$cashPercent}%({$cash_paid}), 承兑({$acceptance_paid})"]);
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
