<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPay;
use App\Models\BillPeriod;
use App\Models\PaymentDetail;
use App\Models\PaymentSchedule;
use App\Models\PaymentType;
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
 * Class PeriodController
 *
 * 付款管理-按账期付款
 *
 * @package App\Admin\Controllers\Pay
 */
class PeriodController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'index' => 'payment.period.pay',
        'flow' => 'pay.period.flow',
        'store'  => 'pay.period.flow.store',
        'update' => 'pay.period.flow.update',
        'destroy' => 'pay.period.flow.destroy',
        'info'    => 'pay.period.flow.info',
    ];

    /**
     * 列表界面
     * @return Content
     */
    public function index()
    {
        return Admin::content(function(Content $content){

            $content->header(trans('pay.period.flow'));

            $content->description('账期');

            $content->breadcrumb(
                ['text'=>trans('payment.index'), 'url'=>'#'],
                ['text'=>trans('payment.period.pay'), 'url'=> $this->getUrl('index')]
            );

            $content->body($this->grid());

        });
    }

    /**
     * 付款管理界面
     */
    public function flow()
    {
        return Admin::content(function(Content $content){

            $content->header(trans('pay.period.flow'));

            $content->description('付款明细');

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'按账期付款', 'url'=> $this->getUrl('index')],
                ['text'=>'付款明细', 'url'=> $this->getUrl('flow')]
            );

            $inputs = Input::get();

            $bill_period_id = isset($inputs['id'])?$inputs['id']:0;

            // 设置付款计划
            $bill_period = BillPeriod::query()->findOrFail($bill_period_id);

            // dd($bill_period);
            $bill_period_other = [
                'paid_money' => BillPay::query()->where('payment_schedule_id', 0)->where('bill_period_id', $bill_period_id)->sum('money'),
                'cash_paid'  => BillPay::query()->where('payment_schedule_id', 0)->where('bill_period_id', $bill_period_id)
                                                ->where('kind', 'cash')->sum('money'),
                'acceptance_paid'  => BillPay::query()->where('payment_schedule_id', 0)->where('bill_period_id', $bill_period_id)
                    ->where('kind', 'acceptance')->sum('money'),
            ];

            // 获取付款模式
            $options = PaymentDetail::getPayTypeOptions();

            $content->row(view('admin.bill.pay', compact('options', 'bill_period', 'bill_period_other')));

            $filter = [];

            $filter['bill_period_id'] = $bill_period_id;

            $content->row(function(Row $row)use($filter){

                $row->column(8, function (Column $column)use($filter) {

                    $panel = new Box('已付款项',$this->bill_period_list($filter).('<hr><h6>相关信息</h6><pre id="aboutFlow"></pre>'));

                    $column->append($panel);
                });

                $row->column(4, function (Column $column)use($filter) {

                    $column->append(new Box('付款', $this->bill_period_pay($filter)));

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
        $pay = BillPay::query()->find($id);

        if(empty($pay))
        {
            return response()->json(['status'=>false, 'message'=>'付款信息未找到~']);
        }

        // 获取导入信息
        return response()->json(['status'=>true, 'message'=>'', 'data'=>$pay->toArray()]);
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
        $pay = BillPay::query()->find($id);

        if(!empty($pay))
        {
            $pay->delete();

            $data= [
                'status'  => true,
                'message' => '已删除',
            ];
        }else{

            $data= [
                'status'  => false,
                'message' => '未找到该记录!',
            ];
        }

        return response()->json($data);
    }

    protected function bill_period_list($filter)
    {
        $query = BillPay::query();

        // 没有关联的计划明细
        $query->where('payment_detail_id', 0)
              ->where('payment_schedule_id', 0);

        if($filter['bill_period_id'])
        {
            $query->where('bill_period_id', $filter['bill_period_id']);
        }

        $pays = $query->get();

        $list = [];
        foreach ($pays as $pay)
        {
            $span = '<span class="showMsg" data-url="'.($this->getUrl('info', ['id'=>$pay->id])).'"></span>';
            $actionUpdate = '<a class="btn btn-default choose-detail" data-id="'.($pay->id).'" data-money="'.($pay->money).'" data-url="'.($this->getUrl('update', ['id'=>$pay->id])).'" title="选中付款"><i class="fa fa-gear"></i>选择</a>';
            $actionRemove = '<a class="btn btn-default detail-delete" data-url="'.($this->getUrl('destroy', ['id'=>$pay->id])).'"><i class="fa fa-trash"></i>删除</a>';

            $actions = '<div class="btn-group btn-group-xs">'
                . ($pay->allowEdit()?$actionUpdate:'')
                . ($pay->allowEdit()?$actionRemove:'')
                . '</div>'
                . $span;

            // 设置操作行
            $list[] = [
                'kind' => trans('pay.kind.'.$pay->kind),
                'code' => $pay->code,
                'date' => $pay->date,
                'money'=> $pay->money,
                'company' => $pay->company,
                'action' =>$actions
            ];
        }

        $table = new Table();

        $table->setHeaders([
            'kind'=>'付款方式','code'=>'付款凭证', 'date'=>'付款时间', 'money'=>'金额','company'=>'收款公司', '操作',
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
        
            var html = '', pay = data.data;
            if(pay)
            {
                html += "创建时间："+pay.created_at+'<br>';
            }
            
            $('#aboutFlow').html(html);
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
            'bill_period_id' => 'required',
            'supplier_id' =>'required',

            'kind' => 'required',
            'money'=>'required',
            'code'=>'required',
        ], [],
            [
                'bill_period_id'    => '账期',
                'supplier_id'=> '供应商',
                'kind'        => '付款类型',
                'money'    => '金额',
                'code'   => '付款凭证'
            ]);

        $billPeriod = BillPeriod::query()->findOrFail($request->bill_period_id);

        $row = $request->input();

        $billPay = new BillPay();

        $billPay->fill($row);

        $billPay->user_id = Admin::user()->id;

        // 保存文件
        $billPay->save();

        session()->flash('success', new MessageBag(['title'=>'付款登记成功！', 'message'=>'']));

        return response()->redirectTo($this->getUrl('flow', ['id'=>$request->bill_period_id]));
    }

    protected function bill_period_pay($filter){
        $form = new Form();

        // 设置提交路径
        $form->action($this->getUrl('store'));

        $form->hidden('_token')
            ->default(csrf_token());

        $form->hidden('bill_period_id')
            ->setWidth(8, 3)
            ->default($filter['bill_period_id']);

        $form->select('kind', '付款方式')
            ->setWidth(8, 3)
            ->options(BillPay::getL5Options('pay',['cash', 'acceptance', 'tele_transfer'], 'kind'));

        $form->select('supplier_id', '供应商')
             ->setWidth(8,3)
             ->options(BillPay::getSupplierOptions());

        $form->select('payment_type_id', '付款类型')
            ->setWidth(8, 3)
            ->options(BillPay::getPaymentTypeOptions());

//        $form->text('company', '收款公司')
//            ->setWidth(8, 3);

        $form->date('date', '付款时间')
            ->setWidth(8, 3);

        $form->currency('money', '金额')
            ->setWidth(8, 3)
            ->prepend('￥');

        $form->text('code', '凭据编码')
            ->setWidth(8, 3);
        //->rules('required');

        $form->textarea('memo', '备注')
            ->setWidth(8, 3)
            ->rows(4);

        return $form;
    }

    protected function grid()
    {
        return Admin::grid(BillPeriod::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            /**
             * 暂不提供的按钮
             *
             * 创建、导出
             */
            $grid->disableCreation();
            $grid->disableExport();

            // // 设置默认账期
            // $defaultBillPeriod = BillPeriod::envCurrent();

            // if($defaultBillPeriod)
            // {
            //     $grid->model()->where('bill_period_id', $defaultBillPeriod->id);
            // }

            $grid->filter(function(Grid\Filter $filter){

                $filter->disableIdFilter();

                $filter->like('name', trans('bill.period.name'));
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
             * 当前账期允许付款
             */
            $that = $this;

            $grid->actions(function(Grid\Displayers\Actions $actions)use($that){

                $billPeriod = $this->row;

                $actions->disableEdit();
                $actions->disableDelete();


                $action_pay = _A(
                    '付款',
                    ['href'=>$that->getUrl('flow', ['id'=>$billPeriod->id])],
                    ['title'=>'按照账期付款']
                );

                $actionList = [];
                if($billPeriod->isActive())
                {
                    array_push($actionList, $action_pay);
                }

                $actions->append(join("&nbsp;&nbsp;", array_reverse($actionList)));

            });


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
        });
    }
}
