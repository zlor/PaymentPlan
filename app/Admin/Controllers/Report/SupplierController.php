<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/6/30
 * Time: 13:50
 */

namespace App\Admin\Controllers\Report;


use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;

class SupplierController extends Controller
{
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

        // 按照供应商来聚合付款数据



        return  view('admin.report.monthly_supplier_payment', compact('page'));
    }

    public function _paymentMonthly()
    {
        return Admin::grid(Supplier::class, function(Grid $grid){

            $grid->column('code', trans('supplier.code'));

            $grid->column('name', trans('supplier.name'));
        });
    }
}