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
     * 付款月度对比分析
     */
    public function paymentMonthly()
    {
        $page = [];

        return  view('admin.report.monthly_supplier_payment', compact('page'));
    }

    public function _paymentMonthly()
    {
        return Admin::grid(Supplier::class, function(Grid $grid){

        });
    }
}