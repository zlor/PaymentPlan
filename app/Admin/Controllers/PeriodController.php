<?php

namespace App\Admin\Controllers;

use App\Models\BillPeriod;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class PeriodController extends Controller
{

    use ModelForm;

    protected $routeMap = [
        'index' => 'bill.period.index',
        'fire'  => 'bill.period.fire',
        'home'  => 'index',
    ];
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('bill.period'));
            $content->description(trans('admin.list'));

            $content->body($this->grid());
        });
    }

    public function fire($id)
    {
        // 当存在激活
        $haveActiveBillPeriod = BillPeriod::haveActive();

        if($haveActiveBillPeriod)
        {
            session()->flash('exception', new MessageBag(['title'=>'已存在激活的账期~']));

            return response()->redirectTo($this->getUrl('home'));
        }

        //
        $billPeriod =  BillPeriod::query()->find($id);

        if(empty($billPeriod->id))
        {
            session()->flash('exception', new MessageBag(['title'=>'未找到指定的账期，请检查后重试~']));

            return redirect()->back();
        }

        $billPeriod->status = BillPeriod::STATUS_ACTIVE;

        $res = $billPeriod->save();

        if($res)
        {
            session()->flash('success', new MessageBag(['title'=>'设置成功']));

            return response()->redirectTo($this->getUrl('home'));
        }else{
            session()->flash('error', new MessageBag(['title'=>'设置失败']));

            return redirect()->back();
        }
    }

    protected function grid()
    {
        // 当存在激活
        $haveActiveBillPeriod = BillPeriod::haveActive();

        return Admin::grid(BillPeriod::class, function (Grid $grid)use($haveActiveBillPeriod) {

            // 设置操作栏
            $that = $this;
            $grid->actions(function(Grid\Displayers\Actions $actions)use($that,$haveActiveBillPeriod){

                $billPeriod = $this->row;

                $actions->disableEdit();
                $actions->disableDelete();

                $action_fire = _A('激活',[
                    'href'=>$that->getUrl('fire', ['id'=> $billPeriod->id]),
                ]);

                // 如果没有激活的并且当前是就绪状态，则允许激活
                if(!$haveActiveBillPeriod && $billPeriod->isStandby())
                {
                    $actions->append($action_fire);
                }
            });

            $grid->id('ID')->sortable();

            // 不需要加载多行操作
            $grid->disableRowSelector();
            // 不需要加载导出按钮
            $grid->disableExport();

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


            $grid->filter(function(Grid\Filter $filter){

                $filter->like('charge_man', trans('bill.period.charge_man'));

            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(BillPeriod::class, function (Form $form) {

            $form->display('id', 'ID');

            // 账期年月
            $form->date('month', trans('bill.period.month'))
                ->format('YYYY-MM')
                ->default(date('Y-m'))
                ->rules('required');

            // 账期名称
            $form->text('name', trans('bill.period.name'))
                ->default(date('Y年m月'))
                ->rules('required');

            // 账期范围
            $form->dateRange('time_begin', 'time_end', trans('bill.period.time'));

            // 现金余额
            $form->currency('cash_balance', trans('bill.period.cash_balance'))->prepend('￥');

            // 确认收款(已收发票总额)
            $form->currency('invoice_balance', trans('bill.period.invoice_balance'))->prepend('￥');

            // 预计收款
            $form->currency('except_balance', trans('bill.period.except_balance'))->prepend('￥');

            // 承兑额度
            $form->currency('acceptance_line', trans('bill.period.acceptance_line'))->prepend('￥');

            $form->divider();

            $form->display('status_txt', trans('bill.period.status'))
                    ->default(trans('bill.period.status.'.BillPeriod::STATUS_STANDYBY));

            $form->hidden('status');

            // 负责人
            $form->text('charge_man', trans('bill.period.charge_man'))
                ->rules('required');

            $form->divider();

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $form->saving(function(Form $form){
                // 状态
                $form->status = BillPeriod::STATUS_STANDYBY;
            });
        });
    }
}
