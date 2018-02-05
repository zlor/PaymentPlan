<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GetSpanMoney;
use App\Models\BillPeriod;
use App\Models\PaymentDetail;
use App\Models\PaymentSchedule;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

/**
 * Class DetailController
 *
 * 付款管理-付款明细入口
 *
 * @package App\Admin\Controllers\Pay
 */
class DetailController extends Controller
{
    use ModelForm;

    use GetSpanMoney;

    protected $routeMap = [
        'index' => 'payment.schedule.pay',
        'detail' => 'pay.schedule.detail',
        'store'  => 'pay.schedule.detail.store',
        'update' => 'pay.schedule.detail.update',
        'destroy' => 'pay.schedule.detail.destroy',
        'info'    => 'pay.schedule.detail.info',
    ];

    /**
     * 列表界面
     * @return Content
     */
    public function index()
    {
        return Admin::content(function(Content $content){
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

td>div>ul.list-unstyled>li.show-info>div{
    
    white-space: nowrap;
}
td .text-money{
    color:black;
}
td .text-money.text-unfocus{
    color:#777777;
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

            $content->header(trans('pay.payment.schedule'));

            $content->description('付款计划');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'付款录入', 'url'=> $this->getUrl('index')]
            );

            $content->body($this->grid());

        });
    }

    /**
     * 付款管理界面
     */
    public function detail()
    {
        return Admin::content(function(Content $content){

            $content->header(trans('pay.payment.schedule.detail'));

            $content->description('付款明细');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'付款录入', 'url'=> $this->getUrl('index')],
                ['text'=>'付款明细', 'url'=> $this->getUrl('detail')]
            );

            $inputs = Input::get();

            $payment_schedule_id = isset($inputs['id'])?$inputs['id']:0;

            // 设置付款计划
            $paymentSchedule = PaymentSchedule::query()->findOrFail($payment_schedule_id);

            $bill_period = $paymentSchedule->bill_period;

            // 获取付款模式
            $options = PaymentDetail::getPayTypeOptions();

            $content->row(view('admin.bill.pay', compact('options', 'paymentSchedule', 'bill_period')));

            $filter = [];

            $filter['payment_schedule_id'] = $payment_schedule_id;

            $content->row(function(Row $row)use($filter){

                $row->column(8, function (Column $column)use($filter) {

                    $panel = new Box('已付款项',$this->schedule_detail_list($filter).('<hr><h6>相关信息</h6><pre id="aboutDetail"></pre>'));

                    $column->append($panel);
                });

                $row->column(4, function (Column $column)use($filter) {

                    $column->append(new Box('付款', $this->schedule_detail_pay($filter)));

                });
            });

        });
    }

    /**
     * 获取文件相关信息
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info($id)
    {
        $detail = PaymentDetail::query()->find($id);

        if(empty($detail))
        {
            return response()->json(['status'=>false, 'message'=>'付款信息未找到~']);
        }

        // 获取导入信息
        return response()->json(['status'=>true, 'message'=>'', 'data'=>$detail->toArray()]);
    }

    /**
     * 将指定的文件，删除
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $detail = PaymentDetail::query()->find($id);

        if(!empty($detail))
        {
            $detail->delete();

            $data= [
                'status'  => true,
                'message' => '已删除',
            ];
        }else{

            $data= [
                'status'  => false,
                'message' => '未找到该文件!',
            ];
        }

        return response()->json($data);
    }

    protected function schedule_detail_list($filter)
    {
        $query = PaymentDetail::query();

        if($filter['payment_schedule_id'])
        {
            $query->where('payment_schedule_id', $filter['payment_schedule_id']);
        }

        $details = $query->get();

        $list = [];
        foreach ($details as $detail)
        {
            $span = '<span class="showMsg" data-url="'.($this->getUrl('info', ['id'=>$detail->id])).'"></span>';
            $actionUpdate = '<a class="btn btn-default choose-detail" data-id="'.($detail->id).'" data-name="'.($detail->code).'" data-url="'.($this->getUrl('update', ['id'=>$detail->id])).'" title="选中付款"><i class="fa fa-gear"></i>选择</a>';
            $actionRemove = '<a class="btn btn-default detail-delete" data-url="'.($this->getUrl('destroy', ['id'=>$detail->id])).'"><i class="fa fa-trash"></i>删除</a>';

            $actions = '<div class="btn-group btn-group-xs">'
                . ($detail->allowEdit()?$actionUpdate:'')
                . ($detail->allowEdit()?$actionRemove:'')
                . '</div>'
                . $span;

            // 设置操作行
            $list[] = [
                'pay_type' => trans('payment.detail.pay_type.'.$detail->pay_type),
                'code' => $detail->code,
                'time' => $detail->time,
                'money'=> $detail->money,
                'collecting_company' => $detail->collecting_company,
                'action' =>$actions
            ];
        }

        $table = new Table();

        $table->setHeaders([
            'pay_type'=>'付款方式','code'=>'付款凭证', 'time'=>'付款时间', 'money'=>'金额','collecting_company'=>'收款公司', '操作',
        ]);

        $table->setRows($list);

        $script = <<<SCRIPT
$(function () {
    function activeTr(tr){
        $('.table').find('tr').removeClass('active');
        $('.table').find(tr).addClass('active');
    }
    
    function showMsgTr(tr){
        $('.table').find('tr').removeClass('focus');
        $('.table').find(tr).addClass('focus');
    }
    
    $('tbody tr').click(function(){
        var url = $(this).find('span.showMsg').data('url'),
            params = {};
            
        params._token = LA.token;
        
        showMsgTr($(this));
        
        $.get(url, params, function(data){
        
            var html = '', detail = data.data;
            if(detail)
            {
                html += "创建时间："+detail.created_at+'<br>';
            }
            
            $('#aboutDetail').html(html);
        }, 'json');
    });
    $('.choose-detail').click(function() {
        var url = $(this).data('url');
        
    });
    $('.detail-delete').click(function () {
        var url  = $(this).data('url');
        
        swal({
            title: "确认删除?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        },
        function(){
            $.ajax({
                method: 'delete',
                url: url,
                data: {
                    _token:LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.status) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                    }
                }
            });
        });
    });
});
SCRIPT;

        Admin::script($script);


        return $table;
    }


    /**
     * 保存数据
     */
    public function store(Request $request)
    {
        // 验证上传信息
        $this->validate($request, [
            'payment_schedule_id' => 'required',
            'pay_type' => 'required',
            'money'=>'required',
            'code'=>'required',
        ], [],
            [
                'payment_schedule_id'              => '付款计划',
                'pay_type'              => '付款类型',
                'money'    => '金额',
                'code'   => '付款凭证'
            ]);

        $paymentSchedule = PaymentSchedule::query()->findOrFail($request->payment_schedule_id);

        $row = $request->input();

        $paymentDetail = new PaymentDetail();

        $paymentDetail->fill($row);

        $paymentDetail->bill_period_id = $paymentSchedule->bill_period_id;

        $paymentDetail->supplier_id = $paymentSchedule->supplier_id;

        $paymentDetail->user_id = Admin::user()->id;

        // 保存文件
        $paymentDetail->save();

        session()->flash('success', new MessageBag(['title'=>'付款登记成功！', 'message'=>'']));

        return response()->redirectTo($this->getUrl('detail', ['id'=>$paymentSchedule->id]));
    }

    protected function schedule_detail_pay($filter){

        $paymentSchedule = PaymentSchedule::query()->findOrNew($filter['payment_schedule_id']);

        $form = new Form();

        // 设置提交路径
        $form->action($this->getUrl('store'));

        $form->hidden('_token')
            ->default(csrf_token());

        $form->hidden('payment_schedule_id')
            ->setWidth(8, 3)
            ->default($filter['payment_schedule_id']);

        $form->select('supplier_id', '收款公司')
            ->setWidth(8, 3)
            ->options(PaymentSchedule::getSupplierOptions())
            ->default(strval($paymentSchedule->supplier_id))
            ->readOnly();

        $form->select('pay_type', '付款方式')
            ->setWidth(8, 3)
            ->options(PaymentDetail::getPayTypeOptions());

        $form->text('code', '付款凭证')
            ->setWidth(8, 3)
            ->rules('required');

        $form->date('time', '付款时间')
            ->setWidth(8, 3);

        $form->currency('money', '金额')
            ->setWidth(8, 3)
            ->prepend('￥');

        $form->textarea('memo', '备注')
            ->setWidth(8, 3)
            ->rows(4);

        return $form;
    }

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

            // 过滤不可付款的计划
            // 应付金额为0 时，不可付款
            $grid->model()->where('due_money', '>', 0);

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


                $action_pay = _A(
                    '付款',
                    ['href'=>$that->getUrl('detail', ['id'=>$paymentSchedule->id])],
                    ['title'=>'依照付款计划付款']
                );

                $actionList = [];
                if($paymentSchedule->hasLockInfo())
                {
                    array_push($actionList, $action_pay);
                }

                $actions->append(join("&nbsp;&nbsp;", array_reverse($actionList)));

            });

            // 账期
            $grid->column('bill_period.name', trans('payment.schedule.bill_period'));
            // 物料类型
            $grid->column('payment_type.name', trans('payment.schedule.payment_type'));

            $that = $this;
            /**
             * 导入信息汇总
             */
            // 供应商
            $grid->column('supplier_name', trans('payment.schedule.supplier_name'))
                ->display(function($value){
                    // $txt = mb_strlen($value)>10?(mb_substr($value, 0, 10).'...'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$value}</span>";
                });
            // 科目
            $grid->column('name', trans('payment.schedule.name'));
            // 物料名称
            $grid->column('materiel_name', trans('payment.schedule.payment_materiel'))
                ->display(function($value){
                    // $txt =  mb_strlen($value)>4?(mb_substr($value, 0, 4).'..'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$value}</span>";
                });
            // 付款确认人
            $grid->column('charge_man', trans('payment.schedule.charge_man'));

            // 付款周期
            $grid->column('pay_cycle', trans('payment.schedule.pay_cycle'))
                ->display(function($value){
                    // $txt =  mb_strlen($value)>6?(mb_substr($value, 0, 6).'..'):$value;
                    return "<span data-toggle='tooltip' data-title='{$value}'>{$value}</span>";
                });

            // 上期未付清
            $grid->column('supplier_lpu_balance', trans('payment.schedule.supplier_lpu_balance'))
                ->display(function()use($that){
                    return $that->_getMoneySpan($this->supplier_lpu_balance, ['moneyClass'=>'text-unfocus']);
                });

            // 供应商全款余额
            $grid->column('supplier_balance', trans('payment.schedule.supplier_balance'))
                ->display(function()use($that){
                    return $that->_getMoneySpan($this->supplier_balance, ['title'=>'']);
                });

            // 计划相关信息
            $grid->column('planInfo', trans('payment.schedule.planInfo'))
                ->display(function($value)use($that){
                    return  $that->_getMoneySpan($this->plan_due_money, ['title'=>"{$this->plan_man},{$this->plan_time}", 'moneyClass'=>'text-unfocus']);
                });

            // 核定相关信息
            $grid->column('auditInfo', trans('payment.schedule.auditInfo'))
                ->display(function($value)use($that){
                    return $that->_getMoneySpan($this->audit_due_money, ['title'=>"{$this->audit_man},{$this->audit_time}",'moneyClass'=>'text-unfocus']);
                });

            // 终核相关信息
            $grid->column('finalInfo', trans('payment.schedule.finalInfo'))
                ->display(function($value)use($that){
                    return $that->_getMoneySpan($this->final_due_money, ['title'=>"{$this->final_man},{$this->final_time}", 'moneyClass'=>'text-unfocus']);
                });

            // 应付款
            $grid->column('due_money', trans('payment.schedule.due_money'))
                ->display(function($value)use($that){
                    return $that->_getMoneySpan($value);
                });

            // 已付款
            $grid->column('paid_money', trans('payment.schedule.paid_money'))
                ->display(function($value)use($that){

                    $html = '';

                    if($this->hasPayInfo())
                    {
                        $cash_paid = number_format($this->cash_paid, 2);
                        $acceptance_paid = number_format($this->acceptance_paid, 2);

                        $options['title'] = "现金:{$cash_paid},承兑:{$acceptance_paid}";

                        $html = $that->_getMoneySpan($this->paid_money, $options);
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
