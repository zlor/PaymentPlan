<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/6/30
 * Time: 13:50
 */

namespace App\Admin\Controllers\Report;


use App\Http\Controllers\Controller;
use App\Models\BillPay;
use App\Models\Supplier;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class SupplierController extends Controller
{

    protected $routeMap = [
        'yearly' => 'report.supplier.payment.year'
    ];

    /**
     * 指定年度- 每月付款统计
     */
    public function paymentByYear()
    {
        return Admin::content(function(Content $content){
            $content->header('付款数据报表');
            $content->description('年度付款分析');

            $content->breadcrumb(
                ['text'=>'分析报表'],
                ['text'=>'付款数据报表', 'url'=>$this->getUrl('yearly')]
            );

            $grid = $this->_gridPaymentByYear();

            $content->body($grid);
        });
    }

    public function _gridPaymentByYear()
    {
        return Admin::grid(BillPay::class, function(Grid $grid){

            $grid->disableCreateButton();
            $grid->disableExport();

            $grid->tools(function(Grid\Tools $tools){
                $tools->disableBatchActions();
            });
            $grid->disableRowSelector();
            $grid->actions(function(Grid\Displayers\Actions $actions){
                $actions->disableEdit();
                $actions->disableDelete();
            });

            $grid->column('yearReport', '年');
            //            $grid->column('monthReport', '月');
            $grid->column('moneySum',  '总付款');

            // 供应商名称
            $grid->column('supplier.name',  '供应商名称');

            // 扩展信息
            $grid->column('expand', '更多')
                ->expand(function(){
                    return '';
                }, '对比数据');

            ### 检索条件
            $grid->filter(function(Grid\Filter $filter){
                $filter->disableIdFilter();

                $filter->like("date", '年')
                        ->select(BillPay::getReportYearOptions());
                $filter->equal('supplier_id', '供应商')
                        ->select(BillPay::getSupplierOptions());
            });
            $map = [];
            for($i =1; $i<=12; $i++)
            {
                $map[$i] = ['key'=>"m{$i}", 'text'=>"{$i}月"];
            }
            $fields =  ['supplier_id',
                DB::raw("supplier_id as id"),
                DB::raw("year(date) as yearReport"),
                // DB::raw("month(date) as monthReport"),
                DB::raw("SUM(money) as moneySum")
            ];
            foreach ($map as $key => $item)
            {
                $fields[]= DB::raw("SUM(case month(date)  when {$key} then money else 0 end) as {$item['key']} ");

                // 动态显示月份的数据
                $grid->column($item['key'], $item['text'])->display(function($value){
                    return trim($value,'.00' );
                });
            }

            $groupBys = ['supplier_id'
                , DB::raw("year(date)")
                //  , DB::raw("month(date)")
            ];
            $grid->model()
                ->select($fields)
                ->groupBy($groupBys);

            $grid->rows(function(Grid\Row $row){
                // 计算扩展数据
                // - 当前应付款余额  use  $model['xxxx'] = 'xxx'的形式即可
                $row->setAttributes(['test'=>'sds']);

                $model = $row->model();
                $model['test']= 'sdsd';
//                dd($row);
            });

        });
    }

    /**
     * 月度付款分析
     * 1.  供应商付款数据：
     * 筛选 时间范围(展示为列，按[年/月]聚合分列) 、供应商类别、供应商相关、 实付\应付，
     * 扩展 对应月度折线图(供应商月度数据之间的变动趋势)
     *
     * 扩展 关联应付数据分析(给出对比背景,当期 实付/应付 比率)
     *
     * 2.  付款类别付款数据:
     * 同上
     *
     * 3. 未付款累积图
     */
    public function paymentMonthly()
    {
        $page = [];

        return $this->_billPay();
        return  view('admin.report.monthly_supplier_payment', compact('page'));
    }

    public function _paymentMonthly()
    {
        return Admin::grid(Supplier::class, function(Grid $grid){

            $grid->column('code', trans('supplier.code'));

            $grid->column('name', trans('supplier.name'));
        });
    }

    /**
     * 付款信息
     */
    public function _billPay()
    {
        $inputs = Input::all();

        // 对比分析的方式： ringRatio 环比， year-on-year 同比
        $filter_compareType = isset($inputs['compareType'])?$inputs['compareType']:'';

        // 分析年份， 默认为当前年份
        $filter_analysisYear = isset($inputs['year'])?$inputs['year']:date('Y');

        // 对比分析的模式：singleLine 单行对比，
        $filter_compareMode = isset($inputs['compareMode'])?$inputs['compareMode']:'';

        $filter_supplierName = isset($inputs['supplier_name'])?$inputs['supplier_name']:'';


        $suppliers = Supplier::query()
            ->where('name', 'like', "%{$filter_supplierName}%")
            ->get();

        // 获取需要统计的列
        $fields = [];
        // - 按照年/月划分
        // 按照 供应商聚合
        $query = BillPay::query();
        $query->select(['supplier_id',
            DB::raw("year(date)"),
            DB::raw("month(date)"),
            DB::raw("SUM(money)")
        ]);
        if($suppliers->count()>0)
        {
            $query->whereIn('supplier_id', array_column($suppliers->toArray(), 'id'));
        }
        $query->groupBy(['supplier_id'
            , DB::raw("year(date)")
            , DB::raw("month(date)")
        ]);

        $billPays =$query->paginate();

        return Response::json($billPays);
    }
}