<?php

namespace App\Admin\Controllers;

use App\Models\Supplier;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SupplierController extends Controller
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
        return Admin::content(function (Content $content) {

            $content->header(trans('supplier.index'));
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
        return Admin::form(Supplier::class, function (Form $form) {

            $form->display('id', 'ID');

            // 名称
            $form->text('name', trans('supplier.name'));

            // 抬头
            $form->text('head', trans('supplier.head'));

            // 标识号
            $form->text('code', trans('supplier.code'));

            // 联系人
            $form->text('contact', trans('supplier.contact'));

            // 联系方式
            $form->mobile('tel', trans('supplier.tel'));

            // 联系地址
            $form->text('address', trans('supplier.address'));

            // 供应商所有人
            // TODO 使用自动完成控件
            $form->select('owner', trans('supplier.owner'))
                ->options(Supplier::getOwnerOptions());

            $form->divider();

            // 供应商logo
            $form->image('logo', trans('supplier.logo'));

            // 备注
            $form->textarea('memo', trans('supplier.memo'))->rules('nullable');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
