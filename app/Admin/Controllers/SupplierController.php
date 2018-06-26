<?php

namespace App\Admin\Controllers;

use App\Admin\Layout\CustomContent;
use App\Models\Supplier;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class SupplierController extends Controller
{
    use ModelForm;

    protected $routeMap = [
        'fastCreateMateriel' =>'base.bill.payment_materiel.create',
        'fastCreateSupplier' =>'base.supplier.create',
        'reloadMaterielOptions'=>'select.payment_materiel.options',
        'reloadSupplierOptions'=>'select.payment_supplier.options',
    ];
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('supplier.index'));
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


    public function one(){

        $params = Input::all();

        $id = $params['id'];

        $supplier = Supplier::query()->find($id);

        $data = [
            'status'=> 'fail',
            'result'=> $supplier,
            'msg'   => '',
        ];

        if(!empty($supplier))
        {
            $data['status'] = 'succ';
        }

        return Response::json($data);
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Supplier::class, function (Grid $grid) {

            // 关闭多行操作
            $grid->disableRowSelector();
            // 关闭导出按钮
            $grid->disableExport();

            $grid->id('ID')->sortable();

            $grid->column('name', trans('supplier.name'));

            $grid->column('code', trans('supplier.code'));

            $grid->column('head', trans('supplier.head'));

            $grid->column('contact', trans('supplier.contact'));

            $grid->column('tel', trans('supplier.tel'));

            $grid->column('address', trans('supplier.address'));

            $grid->column('owner.name', trans('supplier.owner'));

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

        return Admin::form(Supplier::class, function (Form $form)use($inputs) {

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

            // 标识号
            $form->text('code', trans('supplier.code'))
                ->help("计划(科目编码)");

            // 名称
            $form->text('name', trans('supplier.name'))->rules('required')
                ->help('计划(供应商)');

            // 抬头
//            $form->text('head', trans('supplier.head'))->rules('required');

            $form->divider();

            //付款相关
            //付款类型
            $form->select('payment_type_id', trans('payment.types'))
                ->rules('required')
                ->options(Supplier::getPaymentTypeOptions())
                ->help('计划(付款类型)');

            // 快速新增物料
            $textA = '计划(物品名称)   &nbsp;|&nbsp; <span><i class="fa fa-plus text-green"></i>'
                ._A(
                    " 新增物料 "
                    ,['class'=>'text-green', 'id'=>'fastMaterielAction']
                    ,[
                        'url'=>$this->getUrl('fastCreateMateriel', ['useFast'=>1])
                        , 'reloadOptionsUrl'=>$this->getUrl('reloadMaterielOptions')
                        , 'targetName'=>'payment_materiel_id'
                    ]
                ).'</span>';
            Admin::script(view("admin.base.materiel_fast_action", [
            ])->render());
            //付款物料
            $form->select('payment_materiel_id', trans('payment.materiels'))
                ->options(Supplier::getPaymentMaterielOptions())
                ->rules('required')
                ->help($textA);

            // 付款周期月份数
            $form->number('months_pay_cycle', trans('supplier.months_pay_cycle'))
                ->default(3)
                ->help('计划(付款周期)');

            $form->text('charge_man', trans('supplier.charge_man'))
                    ->rules('required')
//                    ->prepend(' <i class="text-red fa fa-check" title="必填"></i> ')
                    ->help('计划(付款确认人)');

            //条约
            $form->textarea('terms', trans('supplier.terms'))
            ->help('计划(合同)');

            $form->divider();

            // 供应商所有人
            //            // TODO 使用自动完成控件
            //            $form->select('supplier_owner_id', trans('supplier.owner'))
            //                ->options(Supplier::getOwnerOptions());

            // 联络人
            $form->text('contact', trans('supplier.contact'));

            // 联系方式
            $form->mobile('tel', trans('supplier.tel'));

            // 联系地址
            $form->text('address', trans('supplier.address'));

//            $form->divider();
            // 供应商logo
//            $form->image('logo', trans('supplier.logo'));

            // 备注
            $form->textarea('memo', trans('supplier.memo'))->rules('nullable');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');


            $form->saving(function(Form $form){
                // code 不为空
                if(empty($form->code))
                {
                    $form->input('code', '');
                }
            });
        });
    }
}
