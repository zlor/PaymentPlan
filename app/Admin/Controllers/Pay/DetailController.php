<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
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

            // 获取付款模式
            $options = PaymentDetail::getPayTypeOptions();

            $content->row(view('admin.bill.pay', compact('options', 'paymentSchedule')));

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
        $form = new Form();

        // 设置提交路径
        $form->action($this->getUrl('store'));

        $form->hidden('payment_schedule_id')
            ->setWidth(8, 3)
            ->default($filter['payment_schedule_id']);

        $form->select('pay_type', '付款方式')
            ->setWidth(8, 3)
            ->options(PaymentDetail::getPayTypeOptions());

        $form->text('collection_company', '收款公司')
            ->setWidth(8, 3);

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
        });
    }
}
