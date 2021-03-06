<?php

namespace App\Admin\Controllers;

use App\Admin\Layout\CustomContent;
use App\Models\Supplier;

use App\Models\SupplierBalanceFlow;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class SupplierBalanceFlowController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'fastCreateMateriel' =>'base.bill.payment_materiel.create',
        'fastCreateSupplier' =>'base.supplier.create',
        'reloadMaterielOptions'=>'select.payment_materiel.options',
        'reloadSupplierOptions'=>'select.payment_supplier.options',
    ];
    /**
     * 供应商账户金额流
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('supplier.balance.init.index'));
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
        $flow = SupplierBalanceFlow::query()->find($id);

        if($flow->type != 'init')
        {
            return false;
        }

        return Admin::content(function (Content $content) use ($id) {

            $content->header(trans('supplier.index'));
            $content->description(trans('admin.edit'));

            $content->body($this->form()->edit($id));
        });
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


//    public function one(){
//
//        $params = Input::all();
//
//        $id = $params['id'];
//
//        $supplier = Supplier::query()->find($id);
//
//        $data = [
//            'status'=> 'fail',
//            'result'=> $supplier,
//            'msg'   => '',
//        ];
//
//        if(!empty($supplier))
//        {
//            $data['status'] = 'succ';
//        }
//
//        return Response::json($data);
//    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(SupplierBalanceFlow::class, function (Grid $grid) {

            $grid->filter(function (Grid\Filter $filter){
                $filter->like('supplier.name', trans('supplier.name'));

            });

            // 关闭多行操作
            $grid->disableRowSelector();
            // 关闭导出按钮
            $grid->disableExport();

            $grid->actions(function(Grid\Displayers\Actions $actions){
                $flow = $actions->row;

                if( ! in_array($flow->type, ['init']))
                {
                    $actions->disableEdit();
                    $actions->disableDelete();
                }
            });

            $grid->id('ID')->sortable();

            $grid->column('supplier.name', trans('supplier.name'));

            //trans('supplier.code')
            $grid->column('supplier.code', '科目编码');

            $grid->column('type', trans('supplier.balance.flow.type'))
                ->display(function($value){
                    return $value?trans('supplier.balance.flow.type.'.$value):'';
                });

            $grid->column('year', trans('supplier.balance.flow.year'));

            $grid->column('month', trans('supplier.balance.flow.month'));

            $grid->column('date', trans('supplier.balance.flow.date'));

            $grid->column('money', trans('supplier.balance.flow.money'));

            $grid->created_at();
            $grid->updated_at();
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

            // 年
            $form->number('year', trans('supplier.balance.flow.year'))
                ->default(date('Y'))
                ->rules('required');

            // 月
            $form->number('month', trans('supplier.balance.flow.month'))
                ->default(date('m'))
                ->rules('required');

            // 发生时间
            $form->currency('money', trans('supplier.balance.flow.money'))
                ->symbol('￥')
                ->rules('required');

            // 发生日期
            $form->date('date', trans('supplier.balance.flow.date'))
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
