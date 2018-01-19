<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentType;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Widget;

class HomeController extends Controller
{

    protected $routeMap = [
        'index' => 'admin.index',
        'gather_bill_period' => 'admin.bill.gather',
        'query' => 'admin.bill.period.query',
        'base.bill_periods'   => 'bill_periods.index',
        'base.bill_period.view' => 'bill_periods.show',
        'base.bill_period.edit' => 'bill_periods.edit',
    ];

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('');
            $content->description('');

            $content->row($this->account_period());


        });
    }

    protected function account_period()
    {
        // return new InfoBox('账期', 'fa fa-bill', 'blue','', '七月');
        $boxAccountPeriod = new Box('账期', '一月');

        return $boxAccountPeriod;
    }


    /**
     * 账期汇总信息
     * @return Content
     */
    public function indexGatherBillPeriod()
    {
        return Admin::content(function(Content $content){

            $content->header('账期');

            $content->description('汇总信息');

            // 添加面包屑导航
            $content->breadcrumb(
                ['text' => '付款管理', 'url' => '#'],
                ['text' => '账期总览', 'url' => $this->getUrl('gather_bill_period')]
            );

            $content->row(function (Row $row) {

                $row->column(3, function (Column $column) {
                    // 账期列表
                    $column->append($this->_gatherPartList());
                });

                $row->column(9, function (Column $column) {

                    // 账期基本信息
                    $column->append($this->_gatherPartInfo());

                    // 账期其他汇总信息(供应商, 应付款, 已付款)

                    // $column->append(Dashboard::environment());
                    // $column->append(Dashboard::extensions());
                });

            });

            // 增加对 _gatherPartList 列表的触发事件的处理
            $script = <<<SCRIPT
SCRIPT;

            Admin::script($script);

            $content->row(view('admin.bill.gather', compact('billPeriod')));
        });
    }

    /**
     * 获取账期列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function _gatherPartList()
    {
        $items = BillPeriod::query()->orderBy('month', 'desc')->get();

        foreach ($items as & $item)
        {
            $item->view = $this->getUrl('base.bill_period.view', ['id'=>$item->id]);
            $item->edit = $this->getUrl('base.bill_period.edit', ['id'=>$item->id]);
        }

        $page = [
            'url'=>[
                'baseBillPeriod' => $this->getUrl('base.bill_periods')
            ]
        ];

        return view('admin.bill.gather_list', compact('items', 'page'));
    }

    protected function _gatherPartInfo()
    {
        $paymentTypes = PaymentType::query()->get();

        $payment = [
            'supplier_count' => 0,
            'due_money_sum'  => 800000,
            'paid_money_sum'  => 400000,
        ];
        foreach ($paymentTypes as &$type)
        {
            $type->supplier_count = 0;
            $type->due_money_sum = 2000000;
            $type->paid_money_sum = 1000000;
        }

        $schedule = $this->_gatherPartSchedule();

        $detail  = $this->_gatherPartDetail();

        return view('admin.bill.gather_info', compact('payment','paymentTypes','schedule','detail','page'));
    }

    protected function _gatherPartSchedule()
    {
        // table 1
        $headers = ['编号','供应商', '物品','应付', '已付',];
        $rows = [
            ['21210226', '苏州美德航空航天材料有限公司', '铝板', '20000', '1000'],
            ['21210008', '山东兖矿轻合金有限公司', '铝板', '20000','100'],
        ];

        $table = new Table($headers, $rows);

        // return $table->render();
        return 'TODO 状态履历信息';
    }

    protected function _gatherPartDetail()
    {

// table 2
        $headers = ['Keys', 'Values'];
        $rows = [
            'name'   => 'Joe',
            'age'    => 25,
            'gender' => 'Male',
            'birth'  => '1989-12-05',
        ];

        $table = new Table($headers, $rows);

        // return $table->render();
        return 'TODO 快速操作';
    }


}
