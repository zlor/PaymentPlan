<?php

namespace App\Admin\Controllers;

use App\Models\PaymentDetail;

use App\Models\PaymentMateriel;
use App\Models\PaymentType;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PaymentTypeController extends Controller
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

            $content->header(trans('payment.type'));
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

            $content->header(trans('payment.type'));
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

            $content->header(trans('payment.type'));
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
        return Admin::grid(PaymentType::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            // 类型名称
            $grid->column('name', trans('payment.type.name'));

            // 类型代号
            $grid->column('code', trans('payment.type.code'));

            // 类型标识
            $grid->column('icon', trans('payment.type.icon'));

            // map_sheet
            $grid->column('sheet_slug', trans('payment.type.sheet_slug'));


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
        return Admin::form(PaymentType::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', trans('payment.type.name'))
                ->rules('required');

            $form->text('code', trans('payment.type.code'))
                ->rules('required');

            $form->icon('icon', trans('payment.type.icon'));

            $form->radio('map_sheet', trans('payment.type.map_sheet'))
                ->options(PaymentType::getBooleanOptions('payment.type.map_sheet', 'bool', true));

            $form->text('sheet_slug', trans('payment.type.sheet_slug'));

            $form->textarea('memo', trans('admin.memo'));

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
