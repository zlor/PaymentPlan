<?php

namespace App\Admin\Controllers;

use App\Models\Supplier;

use App\Models\SupplierOwner;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SupplierOwnerController extends Controller
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

            $content->header(trans('supplier.owner'));
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

            $content->header(trans('supplier.owner'));
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
        return Admin::content(function (Content $content) {

            $content->header(trans('supplier.owner'));
            $content->description(trans('admin.create'));

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(SupplierOwner::class, function (Grid $grid) {

            // 关闭多行操作
            $grid->disableRowSelector();
            // 关闭导出按钮
            $grid->disableExport();

            $grid->id('ID')->sortable();

            $grid->column('name', trans('supplier.owner.name'));

            $grid->column('code', trans('supplier.owner.code'));

            $grid->column('company', trans('supplier.owner.company'));

            $grid->column('tel', trans('supplier.tel'));


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
        return Admin::form(SupplierOwner::class, function (Form $form) {

            $form->display('id', 'ID');

            // 名称
            $form->text('name', trans('supplier.owner.name'))
                ->rules('required');

            // 公司
            $form->text('company', trans('supplier.owner.company'))
                ->rules('required');

            // 标识号
            $form->text('code', trans('supplier.owner.code'));

            // 联系方式
            $form->mobile('tel', trans('supplier.owner.tel'))
                ->rules('required');

            // 备注
            $form->textarea('memo', trans('supplier.owner.memo'))->rules('nullable');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
