<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\UserEnvs;
use App\Models\BillPeriod;
use App\Models\PaymentType;
use App\Models\UserEnv;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Widget;
use Illuminate\Support\MessageBag;
use League\Flysystem\Exception;

class HomeController extends Controller
{

    protected $routeMap = [
        'index' => 'admin.index',
        'gatherIndex' => 'bill.gather',
        'gatherTargetIndex' => 'bill.target.gather',
        'query' => 'admin.bill.period.query',
        'bill_periods_manage'   => 'bill_periods.index',
        'base.bill_period.view' => 'bill_periods.show',
        'base.bill_period.edit' => 'bill_periods.edit',

        'setBillPeriod'  => 'bill.pool.edit',

        'fireBillPeriod'  => 'bill.period.index',


        'excel_page'    => 'payment.plan.excel',
        'schedule_plan_page'=> 'payment.schedule.plan.batch',
        'schedule_audit_page'=> 'payment.schedule.audit.batch',
        'schedule_final_page'=> 'payment.schedule.final.batch',
        'schedule_lock_page'=> 'payment.schedule.lock.batch',
        'schedules_manage' => 'payment_schedules.index',

        'details_manage'  => 'payment_details.index',
        'detail_page'     => 'payment.schedule.pay',
    ];

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('');
            $content->description('');

            $content->row($this->account_period());


        });
    }

    protected function account_period()
    {
        // 设置付款计划
        $billPeriod = UserEnv::getCurrentPeriod(false);

        $statusTxt = empty($billPeriod->status)?'':trans("bill.period.status.{$billPeriod->status}");

        $title = "账期「{$statusTxt}」: {$billPeriod->name}   ({$billPeriod->month}), ({$billPeriod->charge_man})  ";

        $boxAccountPeriod = new Box($title, view('admin.bill.base', compact('billPeriod')));

        return $boxAccountPeriod;
    }


    /**
     * 账期汇总信息
     *
     * @param $id integer
     *
     * @param $type_id integer
     *
     * @return Content
     */
    public function indexGatherBillPeriod($id = 0, $type_id = 0)
    {
        // 设置聚焦的账期
        $focusBillPeriod = BillPeriod::query()->find($id);


        if(empty($focusBillPeriod))
        {
            $focusBillPeriod = UserEnv::getCurrentPeriod(false);
        }

        //异常处理
        if(empty($focusBillPeriod->id))
        {
            session()->flash('error', new MessageBag(['title'=>'请先激活或新建一个账期~']));

            return redirect()->to($this->getUrl('fireBillPeriod'));
        }

        // 识别聚焦的类型
        $focusPaymentType = PaymentType::query()->schedule()->findOrNew($type_id);


        return Admin::content(function(Content $content)use($focusBillPeriod, $focusPaymentType){

            $style =<<<STYLE

STYLE;


            $content->header('账期');

            $content->description('汇总信息');

            // 添加面包屑导航
            $content->breadcrumb(
                ['text' => '付款管理', 'url' => '#'],
                ['text' => '账期总览', 'url' => $this->getUrl('gather_bill_period')]
            );

            $content->row(function (Row $row)use($focusBillPeriod, $focusPaymentType) {

                // 账期列表
                $row->column(3, function (Column $column)use($focusBillPeriod) {

                    $column->append($this->_gatherPartList($focusBillPeriod));
                });

                // 账期基本信息
                $row->column(9, function (Column $column)use($focusBillPeriod, $focusPaymentType) {

                    $column->append($this->_gatherPartInfo($focusBillPeriod, $focusPaymentType));
                });

            });

            // 增加对 _gatherPartList 列表的触发事件的处理
            $script = <<<SCRIPT
SCRIPT;

            Admin::script($script);

            $content->row(view('admin.bill.gather', compact('billPeriod')));
        });
    }

    /**
     * 获取账期列表
     *
     * @param BillPeriod $focusBillPeriod
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function _gatherPartList($focusBillPeriod)
    {
        $items = BillPeriod::query()->orderBy('month', 'desc')->get();

        foreach ($items as & $item)
        {
            $item->view = $this->getUrl('gatherIndex', ['id'=>$item->id, 'type_id'=>0]);

            $item->locateLink = $this->getUrl('gatherTargetIndex', ['id'=> $item->id, 'type_id'=>0]);

            $item->edit = $this->getUrl('base.bill_period.edit', ['id'=>$item->id]);

            $item->focus = ($focusBillPeriod->id == $item->id)?true:false;
        }

        // 页面参数: 账期档案管理 , 目标账期
        $page = [
            'url'=>[
                'baseBillPeriod' => $this->getUrl('bill_periods_manage'),
            ],
            'targetId' => $focusBillPeriod->id,
        ];

        return view('admin.bill.gather_list', compact('items', 'page'));
    }

    /**
     * 汇总信息
     *
     * @param BillPeriod $focusBillPeriod
     *
     * @param PaymentType $focusPaymentType
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function _gatherPartInfo($focusBillPeriod, $focusPaymentType)
    {
        $paymentTypes = PaymentType::query()->schedule()->get();

        $paymentTypes->prepend(new PaymentType([
            'id' => 0,
            'name' => '总计',
        ]));

        $focusBillPeriodId = empty($focusBillPeriod->id)?0:$focusBillPeriod->id;

        $focusTypeId = empty($focusPaymentType->id)?0:$focusPaymentType->id;

        foreach ($paymentTypes as &$type)
        {
            $type->supplier_count = $focusBillPeriod->countSuppliers($type->id);

            $type->due_money_sum  = $focusBillPeriod->sumDueMoney($type->id);

            $type->paid_money_sum = $focusBillPeriod->sumPaidMoney($type->id);

            $typeId = (empty($type->id)?0:$type->id);

            $type->focus = $focusTypeId == $typeId;

            $type->locateLink   = $this->getUrl('gatherTargetIndex', ['id'=>$focusBillPeriodId, 'type_id'=>$typeId]);
        }

        $url = [
            // 设置账期
            'set_cash_pool'     =>$this->getUrl('setBillPeriod', ['id'=>$focusBillPeriodId]),
            // 账期下文件管理
            'file_page'         =>  $this->getUrl('excel_page', ['default_bill_period_id'=>$focusBillPeriodId]),

            // 应付款管理-计划录入
            'schedule_plan_page'=> $this->getUrl('schedule_plan_page'),
            // 应付款管理-一次审核
            'schedule_audit_page'=> $this->getUrl('schedule_audit_page'),
            // 应付款管理-二次审核
            'schedule_final_page'=> $this->getUrl('schedule_final_page'),
            // 应付款管理-应付款敲定
            'schedule_lock_page'=> $this->getUrl('schedule_lock_page'),
            // 档案管理-应付款计划
            'schedules_manage'  => $this->getUrl('schedules_manage'),
            // 档案管理-付款明细
            'details_manage'  => $this->getUrl('details_manage'),
            'detail_page'     => $this->getUrl('detail_page')
        ];
        $statusTxt = trans("bill.period.status.{$focusBillPeriod->status}");

        $count = [
            'uploadNum' => $focusBillPeriod->countFile(['is_upload_success'=>true, 'payment_type_id'=>$focusTypeId]),
            'importNum' => $focusBillPeriod->countFile(['is_import_success'=>true, 'payment_type_id'=>$focusTypeId]),
            'planNum'   => $focusBillPeriod->countSchedules(['status'=>['init', 'web_init', 'import_init'], 'payment_type_id'=>$focusTypeId]),
            'auditNum'   => $focusBillPeriod->countSchedules(['status'=>['check_audit'], 'payment_type_id'=>$focusTypeId]),
            'finalNum'   => $focusBillPeriod->countSchedules(['status'=>['check_final'], 'payment_type_id'=>$focusTypeId]),
            'payingNum'   => $focusBillPeriod->countSchedules(['status'=>['paying'], 'payment_type_id'=>$focusTypeId]),
            'paidNum'   => $focusBillPeriod->countSchedules(['status'=>['paid'], 'payment_type_id'=>$focusTypeId]),

            'cash_balance' => $focusBillPeriod->cash_balance,
            'acceptance_balance'   => $focusBillPeriod->acceptance_line,
            'loan_balance'     =>  $focusBillPeriod->loan_balance,
            'invoice_balance'  => $focusBillPeriod->invoice_balance,

            'init_balance'    => $focusBillPeriod->init_total,
            'balance'         => $focusBillPeriod->pool,
            'paid'            => $focusBillPeriod->paid_total,
            'current_cash_balance' => $focusBillPeriod->current_cash_balance,
            'current_acceptance_balance' => $focusBillPeriod->current_acceptance_balance,
            'cash_paid' => $focusBillPeriod->sumCashPaid(['payment_type_id'=>$focusTypeId]),
            'tt_paid' => $focusBillPeriod->sumTeleTransferPaid(['payment_type_id'=>$focusTypeId]),
            'acceptance_paid' => $focusBillPeriod->sumAcceptancePaid(['payment_type_id'=>$focusTypeId]),
            'cash_paid_total' => $focusBillPeriod->cash_paid,
            'acceptance_paid_total' => $focusBillPeriod->acceptance_paid,
        ];

        $html = [];
        $script =<<<SCRIPT
        $(function(){
            $('.type-list li').click(function(){
                var url = $(this).data('locate');
                $.pjax({
                    url: url,
                    container: '#pjax-container'
                })
                
            });
        });
SCRIPT;
        Admin::script($script);


        $table = new Table();

        $table->setHeaders([
            'status' => '状态',
            'type'=> '分类',
            'money'   => '资金= 现金(存款+贷款) + 额度(承兑+确认应收款)',
            'schedule' => '计划',
            'import' => '文件(已载入/总数)',
        ]);

        $typeName = $focusPaymentType->id?$focusPaymentType->name:'总计';
        $sub_money = $focusPaymentType->id?"<h5>{$typeName}</h5><p>支付：
                            <span class='money text-black' data-toggle='tooltip' data-title='总额'>".number_format($count['cash_paid'] + $count['acceptance_paid'],2)."  = </span>
                            <span class='money text-blue' data-toggle='tooltip' data-title='现金'>( ".number_format($count['cash_paid'],2)." )</span>
                            <span class='money text-green'  data-toggle='tooltip' data-title='承兑'> + ( ".number_format($count['acceptance_paid'], 2)." )</span>
                       </p>":'';
        $table->setRows([
            [   'status' => "<label class='badge label-primary'>{$statusTxt}</label>"
                ,'type'  => $typeName
                ,'money'=>$sub_money."<h5>总计</h5>
                        <p>期初：
                         <span class='money text-black' data-toggle='tooltip' data-title='总额'>".number_format($count['init_balance'],2)."  = </span>
                         <span class='money text-blue' data-toggle='tooltip' data-title='现金(存款)'>( ".number_format($count['cash_balance'],2)."</span>
                         <span class='money text-green' data-toggle='tooltip' data-title='现金(贷款)'> + ".number_format($count['loan_balance'], 2)." )</span>
                         <span class='money text-green' data-toggle='tooltip' data-title='额度(承兑)'> + （".number_format($count['acceptance_balance'], 2)."</span>
                         <span class='money text-light-blue' data-toggle='tooltip' data-title='额度(确认应收款)'>  + ".number_format($count['invoice_balance'],2)."） </span>
                         </p>
                         <p>支付：
                            <span class='money text-black' data-toggle='tooltip' data-title='总额'>".number_format($count['paid'],2)."  = </span>
                            <span class='money text-blue' data-toggle='tooltip' data-title='现金'>( ".number_format($count['cash_paid_total'],2)." )</span>
                            <span class='money text-blue' data-toggle='tooltip' data-title='电汇'>+ ( ".number_format($count['tt_paid'],2)." )</span>
                            <span class='money text-green'  data-toggle='tooltip' data-title='承兑'> + ( ".number_format($count['acceptance_paid_total'], 2)." )</span>
                            
                          </p>
                         <p>当前： 
                         <span class='money text-black' data-toggle='tooltip' data-title='余额'>".number_format($count['balance'], 2)."  = </span>
                         <span class='money text-blue' data-toggle='tooltip' data-title='现金'>( ".number_format($count['current_cash_balance'], 2)." )</span>
                         <span class='money text-green' data-toggle='tooltip' data-title='承兑'> + ( ".number_format($count['current_acceptance_balance'], 2)." ）</span>
                         </p>"
                ,'schedule'=>"<p>
                                <span class='text-right'> {$count['planNum']} <i class='text-gray'>计划</i></span><br>
                                <span class='text-right'> {$count['payingNum']} <i class='text-gray'>付款</i></span><br>
                                <span class='text-right'> {$count['paidNum']} <i class='text-gray'>完成</i></span><br>
                              </p>"
                ,'import'=>"<p>{$count['importNum']}/{$count['uploadNum']}</p>"
            ],
            [
                'status' => "<p>"
                            .($focusBillPeriod->allowSetPool()?"<a href='{$url['set_cash_pool']}' >设置账期</a>":'设置账期(已锁定)')
                            ."</p>"

                ,'type'  => '<p>'._A('档案:付款计划', ['href'=>$url['schedules_manage'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'
                           .'<p>'._A('档案:付款详情', ['href'=>$url['details_manage'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'

                ,'money' => '<p>'._A('付款录入', ['href'=>$url['detail_page'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'

                ,'schedule'=> '<p>'._A('计划录入', ['href'=>$url['schedule_plan_page'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'
                             .'<p>'._A('一次核定', ['href'=>$url['schedule_audit_page'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'
                             .'<p>'._A('二次核定', ['href'=>$url['schedule_final_page'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'
                             .'<p>'._A('应付款敲定', ['href'=>$url['schedule_lock_page'], 'target'=>'_blank', 'class'=>"btn btn-default margin"]).'</p>'

                ,'import'=>'<p>'._A('文件导入管理', ['href'=>$url['file_page'], 'target'=>'_blank', 'class'=>"btn btn-default"]).'</p>'
            ]
        ]);


        $html['table'] = $table->render();

        return view('admin.bill.gather_info', compact('paymentTypes', 'html', 'page'));
    }

}
