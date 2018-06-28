<?php

namespace App\Admin\Controllers;

use App\Admin\Layout\CustomContent;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use App\Models\Supplier;

use App\Models\SupplierBalanceFlow;
use App\Models\SupplierInvoiceGather;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class SupplierInvoiceGatherController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'fastCreateMateriel' =>'base.bill.payment_materiel.create',
        'fastCreateSupplier' =>'base.supplier.create',
        'reloadMaterielOptions'=>'select.payment_materiel.options',
        'reloadSupplierOptions'=>'select.payment_supplier.options',
        'initSchedule' => 'base.supplier.invoice.gather.init',
    ];
    /**
     * 供应商应付款发票汇总
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('supplier.invoice.gather.index'));
            $content->description(trans('admin.list'));

            $content->body($this->grid());
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
        // 若 type == 'init', 则允许修改


        return Admin::content(function (Content $content) use ($id) {

            $content->header(trans('supplier.index'));
            $content->description(trans('admin.edit'));

            $content->body($this->form()->edit($id));
        });
    }

    public function refreshShoot()
    {
        $inputs = Input::all();

        $id = $inputs['id'];
        $billPeriod = BillPeriod::query()->find($id);

        $yearMonth = $billPeriod->month;
        $year = intval(date('Y', strtotime($yearMonth)));
        $month = intval(date('m', strtotime($yearMonth)));

        if($month == 1)
        {
            $year =  $year - 1;
            $month = 12;
        }else{
            $month = $month - 1;
        }

        return $this->_refreshShootByInvoice($year, $month);
    }

    public function initShoot()
    {
        $inputs = Input::all();

        $id = $inputs['id'];
        $billPeriod = BillPeriod::query()->find($id);

        $yearMonth = $billPeriod->month;
        $year = intval(date('Y', strtotime($yearMonth)));
        $month = intval(date('m', strtotime($yearMonth)));

        return $this->_refreshShootBySchedule($year, $month);
    }

    /**
     * 依据登记的应付款发票，构建
     * 聚合快照
     * @param int $year
     * @param int $month
     * @return \Illuminate\Http\JsonResponse
     */
    private function _refreshShootByInvoice($year, $month)
    {
        // 统计所有供应商的指定月份的发票总额，汇入到 invoice_gather_shoots
        $invoiceGatherShoots = [];

        $suppliers = Supplier::all();

        $failSuppliers  = [];
        $count = 0;
        foreach ($suppliers as $supplier)
        {
            // 合计供应商指定月的发票总额
            $invoiceMoney = $supplier->invoice_payments
                ->where('year', $year)
                ->where('month', $month)
                ->sum('money');

            // 保存或更新都
            $invoiceShoot = SupplierInvoiceGather::query()
                ->firstOrNew([
                    'year' => $year,
                    'month'=> $month,
                    'supplier_id'=>$supplier->id,
                ], ['money' => $invoiceMoney]);

            $invoiceShoot->money = $invoiceMoney;

            $flag = $invoiceShoot->save();

            if($flag)
            {
                $invoiceGatherShoots[] = $invoiceShoot;
                $count++;
            }else{
                $failSuppliers[] = $supplier;
            }
        }

        $result= [
            'status' => true,
            'data'    => [
                'year' => $year,
                'month' => $month,
                'count' => $count,
            ],
            'message'=> "统计/更新({$year}-{$month}, {$count}条记录)"
        ];
        return Response::json($result);
    }

    /**
     * 依据指定年月的计划信息，
     * 构建相关供应商涉及的六个月份的发票
     * 聚合快照
     * @param $year
     * @param $mont
     *
     */
    private function _refreshShootBySchedule($year, $month)
    {
        $yearMonth = "{$year}-".($month<10?("0{$month}"):$month);
        /**
         * @var BillPeriod $billPeriod
         */
        $billPeriod = BillPeriod::query()->where('month', $yearMonth)->first();

        if(empty($billPeriod))
        {
            return Response::json(['status'=>false, 'message'=>'指定年月未获取到有效数据']);
        }

        // 统计所有供应商的指定月份的发票总额，汇入到 invoice_gather_shoots
        $invoiceGatherShoots = [];

        $suppliers = Supplier::all();

        $failSuppliers  = [];
        $count = 0;

        $columns = $billPeriod->getMonthNumber(true);

        foreach ($suppliers as $supplier)
        {
            $schedule = PaymentSchedule::query()
                ->where('bill_period_id', $billPeriod->id)
                ->where('supplier_id', $supplier->id)
                ->first();

            if(empty($schedule))
            {
                continue;
            }
            foreach ($columns as $column)
            {
                // 供应商指定月的发票总额
                $invoiceMoney = $schedule->$column;

                $subMonth = intval(substr($column, strlen('invoice_m_')));
                $subYear = ($subMonth>$month)?($year-1):$year;

                // 保存或更新都
                $invoiceShoot = SupplierInvoiceGather::query()
                    ->firstOrNew([
                        'year' => $subYear,
                        'month'=> $subMonth,
                        'supplier_id'=>$supplier->id,
                    ], ['money' => $invoiceMoney]);

                $invoiceShoot->money = $invoiceMoney;

                $flag = $invoiceShoot->save();

                if($flag)
                {
                    $invoiceGatherShoots[] = $invoiceShoot;
                    $count++;
                }else{
                    $failSuppliers[] = $supplier;
                }
            }
        }

        $result= [
            'status' => true,
            'data'    => [
                'year' => $year,
                'month' => $month,
                'count' => $count,
            ],
            'message'=> "统计/更新(账期{$year}-{$month}相关, {$count}条记录)"
        ];
        return Response::json($result);
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        $inputs = Input::all();

        if(isset($inputs['useFast']) && $inputs['useFast']>0)
        {
            return $this->_fastCreate();
        }

        return $this->_createForm($this->form());
    }

    protected function _fastCreate()
    {
        return new CustomContent($this->_createForm($this->form()), true);
    }

    protected function _createForm($form)
    {
        return Admin::content(function (Content $content) use($form){

            $content->header(trans('supplier.index'));
            $content->description(trans('admin.create'));

            $content->body($form);
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(SupplierInvoiceGather::class, function (Grid $grid) {

            $grid->filter(function (Grid\Filter $filter){

                $filter->disableIdFilter();

                $filter->like('supplier.name', trans('supplier.name'));

            });

            $url= $this->getUrl('initSchedule');
            $grid->tools(function(Grid\Tools $tools)use($url){
                $billPeriods = BillPeriod::all();
                $tools->append(view('admin.schedule.init_bill_period', ['url'=>$url, 'billPeriods'=>$billPeriods]));
            });

            // 关闭多行操作
            $grid->disableRowSelector();
            // 关闭导出按钮
            $grid->disableExport();

            $grid->disableCreateButton();

            $grid->disableActions();

            $grid->id('ID')->sortable();

            $grid->column('supplier.name', trans('supplier.name'));

            //trans('supplier.code')
            $grid->column('supplier.code', '科目编码');


            $grid->column('year', trans('supplier.balance.flow.year'));

            $grid->column('month', trans('supplier.balance.flow.month'));

            $grid->column('money', trans('supplier.balance.flow.money'));

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $inputs = Input::all();

        return Admin::form(SupplierBalanceFlow::class, function (Form $form)use($inputs) {

            $form->display('id', 'ID');

            if(isset($inputs['useFast']) && $inputs['useFast']>0)
            {
                $form->hidden('useFast')->value($inputs['useFast']);
                // 取消动作条
                $form->tools(function(Form\Tools $tools){
                    $tools->disableBackButton();
                    $tools->disableListButton();
                });

                $form->ignore('useFast');
                // 设置保存后关闭页面的动作
                $form->saved(function(Form $form){
                    return Response::view('admin.base.pop_close');
                });
            }

            // 类型
            $form->text('type', trans('supplier.balance.flow.type'))
                    ->value('init')
                    ->readonly();

            // 快速新增供应商
            $textA = _A("新增供应商", ['class'=>'text-green', 'id'=>'fastSupplierAction'],['url'=>$this->getUrl('fastCreateSupplier', ['useFast'=>1]), 'reloadOptionsUrl'=>$this->getUrl('reloadSupplierOptions'), 'targetName'=>'supplier_id']);
            Admin::script(view("admin.base.supplier_fast_action",[
                'getSupplierOneUrl' => $this->getUrl('getSupplierOne')
            ])->render());

            // 指定的供应商
            $form->select('supplier_id', trans('invoice.supplier'))
                ->options(SupplierBalanceFlow::getSupplierOptions())
                ->help($textA, 'fa fa-plus text-green')
                ->rules('required');

            //关联的付款类型
            $form->select('payment_type_id', trans('payment.type'))
                ->options(SupplierBalanceFlow::getPaymentTypeOptions())
                ->rules('required');


            // 付款周期月份数
            $form->number('year', trans('supplier.balance.flow.year'))
                ->rules('required')
                ->value(date('Y'));

            $form->number('month', trans('supplier.balance.flow.month'))
                ->rules('required')
                ->value(date('m'));

            $form->currency('money', trans('supplier.balance.flow.money'))
            ->rules('required');

            //备注
            $form->textarea('memo', trans('supplier.balance.flow.memo'));


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $form->hidden('user_id');

            $form->saving(function(Form $form){
                if(empty($form->id))
                {
                    if(empty($form->user_id))
                    {
                        $user = Admin::user();
                        $form->input('user_id', $user->id);
                    }
                }
            });
        });
    }
}
