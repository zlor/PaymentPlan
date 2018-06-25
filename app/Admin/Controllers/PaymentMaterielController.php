<?php

namespace App\Admin\Controllers;

use App\Admin\Layout\CustomContent;
use App\Models\PaymentDetail;

use App\Models\PaymentMateriel;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class PaymentMaterielController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('payment.materiel'));
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

            $content->header(trans('payment.materiel'));
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
        $form = $this->form();
        return new CustomContent($this->_createForm($form), true);
    }

    protected function _createForm($form)
    {
        return Admin::content(function (Content $content)use($form) {

            $content->header(trans('payment.materiel'));
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
        return Admin::grid(PaymentMateriel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            // 物料名称
            $grid->column('name', trans('payment.materiel.name'));

            // 物料代号
            $grid->column('code', trans('payment.materiel.code'));

            // 物料标识
            $grid->column('icon', trans('payment.materiel.icon'));


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

        return Admin::form(PaymentMateriel::class, function (Form $form) use ($inputs){

            $form->display('id', 'ID');

            $form->text('name', trans('payment.materiel.name'))
                ->rules('required');

            $form->text('code', trans('payment.materiel.code'))
                ->rules('required');

            $form->icon('icon', trans('payment.materiel.icon'));

            $form->textarea('memo', trans('admin.memo'));

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            if(isset($inputs['useFast']) && $inputs['useFast']>0)
            {
                $form->hidden('useFast')->value($inputs['useFast']);

                // 取消动作条
                $form->tools(function(Form\Tools $tools){
                    $tools->disableBackButton();
                    $tools->disableListButton();
                });

                // 设置保存后关闭页面的动作
                $form->saved(function(){
                    return Response::view('admin.base.pop_close');
                });

                $form->ignore('useFast');
            }
        });
    }
}
