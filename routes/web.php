<?php
use \Illuminate\Support\Facades\Route;
use  Maatwebsite\Excel\Facades\Excel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::get('testIntval', function(){
//    $str = '1529989121';
//    echo time().'<br>';
//    echo date('Y-m-d h:i:s', 1529989121).'<br>';
//    echo $a = intval($str).'<br>';
//    echo $str.'<br>';
//    echo $str>=$a?'yes':'no';
//});

Route::get('/', function(){

    // 选择账套
    $books = \Encore\Admin\Book\BookModel::query()->get();

    $map = [
        'ranto' => $books->where('code', 'ranto')->first(),
        'sunfen' =>$books->where('code', 'sunfen')->first(),
        'cj' => [],
    ];

    return view('bill_books', compact('books', 'map'));
});

/**
 *
 */
Route::get('offset/suggestDueMoney/{id}', function($id){
    $billPeriod = \App\Models\BillPeriod::query()->findOrFail($id);

    $schedules = \App\Models\PaymentSchedule::query()->where('bill_period_id', $billPeriod->id)->get();

    $month = $billPeriod->getMonthNumber();

    foreach ($schedules as $schedule)
    {
        if( $schedule->pay_cycle_month<=12 &&  $schedule->pay_cycle_month>=1)
        {
            $month = $schedule->pay_cycle_month;
        }

        // 总应付为非正数时，建议应付为0
        if($schedule->supplier_balance<=0)
        {
            $schedule->suggest_due_money = 0;

        }else{
            // 若总应付存在正值，则即使计算结果为负值，也需要记录。观测异常。
            $schedule->suggest_due_money = $billPeriod->guestSuggestDueMoney($schedule->toArray(), $month);
        }

        $schedule->save();
    }
});

/**
 * 获取指定账期下， 按照供应商罗列的 建议应付款 Excel
 */
Route::get('getExcel/{id}', function($id){

    /**
     * @type \App\Models\BillPeriod $billPeriod
     */
    $billPeriod = \App\Models\BillPeriod::query()->findOrFail($id);

    $monthNumber = $billPeriod->getMonthNumber();

    $schedules = \App\Models\PaymentSchedule::query()->where('bill_period_id', $billPeriod->id)->get();

    foreach ($schedules as $schedule)
    {
        $row = [
            "供应商名称"=> $schedule->supplier_name,
            "类型"=> $schedule->payment_type_name,
            "总应付金额"=> $schedule->supplier_balance,
            "建议应付金额"=> $schedule->suggest_due_money,
            "付款周期"=> $schedule->pay_cycle,
            "付款周期差异月份数" => $monthNumber - ($schedule->pay_cycle_month<=$monthNumber?:($schedule->pay_cycle_month-12)),
            "到期月份"=> $schedule->pay_cycle_month . '月',
        ];
        $sheetData[] = $row;
    }

    $sheetName = 'tmp';

    $excel = Excel::create($billPeriod->name . '建议应付款', function($excel)use($sheetData, $sheetName){

        $excel->sheet($sheetName, function($sheet)use($sheetData) {

            $sheet->fromArray($sheetData);
        });
    })->download();
});

/**
 *  创建指定账期的计划,从上一个账期继承
 */
Route::get('buildSehedule/from/{fromId}/to/{toId}/{diffMonth}'
    , function($fromId, $toId, $diffMonth){
	/**
     * @type \App\Models\BillPeriod $fromBillPeriod
     */
    $fromBillPeriod = \App\Models\BillPeriod::query()->findOrFail($fromId);

    /**
     * @type \App\Models\BillPeriod $toBillPeriod
     */
    $toBillPeriod = \App\Models\BillPeriod::query()->findOrFail($toId);

    //$fromMonthNumber = $fromBillPeriod->getMonthNumber();

    //$toMonthNumber = $toBillPeriod->getMonthNumber();
    $schedules = $toBillPeriod->copyScheduleForInit($fromBillPeriod, empty($diffMonth)?1:$diffMonth);

	$sheetName = "Form{$fromBillPeriod->name}_To{$toBillPeriod->name}";
	
    $excel = Excel::create($toBillPeriod->name . '计划总表', function($excel)use($schedules, $sheetName){

        $excel->sheet($sheetName, function($sheet)use($schedules) {

            $sheet->fromArray($schedules);
        });
    })->download();

});


Route::get("bcrypt/{password}", function($password){
    return  bcrypt($password);
});

/**
 * && 已在页面中体现  位置: SupplierInvoiceGatherController@init
 *
 * 刷新供应商应付款发票汇总
 *
 * 0. 获取指定的账期
 * 1. 将指定账期的所有付款计划，关联月的发票汇总刷新到对应的 汇总表中
 *
 */
Route::get("backend/supplier/invoice/gather/{year}/{month}/refresh/{account}/{password}", function($year, $month, $account, $password){
    // 验证账户密码
    if($account == 'admin' && $password == 'admin123')
    {
        // 按照 year month 刷新并生成供应商发票汇总
        return 'web中封闭';
    }else{
        return "帐户名密码不正确";
    }
});

/**
 *  重置供应商应付账户 流水记录
 *
 * 0. 清空所有流水记录
 * 1. 从指定年月开始记录
 * 2. 期初数据，采用指定年月的 付款计划 总应付数据作为 期初数据
 * 3. 从指定年月开始，同步应付款发票记录
 * 4. 从指定年月开始，同步付款记录(包含 账期付款/计划付款)
 *
 * && 该方法的使用条件：从指定年月开始，将以发票数据为计划生成来源（预付款项也应作为 ‘应付款增项‘ 计入到流水中）
 * && 使用账期来关联区分，实际年月。
 *
 */
Route::get('backend/supplier/balance_flow/{year}/{month}/reset/{account}/{password}'
    , function($year, $month,$account, $password){
    //确认身份
    if($account == 'admin' && $password =  'admin123')
    {
        ### 确认指定年月的账期
        $billPeriod = \App\Models\BillPeriod::query()->where('month', date('Y-m', strtotime("{$year}-{$month}")))
            ->first();
        if(empty($billPeriod))
        {
            return "{$year}年{$month}月 未找到对应账期";
        }

        ### 确认将作为所有数据来源的全部账期
        $billPeriods = \App\Models\BillPeriod::query()->where('month', '<=', date('Y-m', strtotime("{$year}-{$month}")))
            ->get();
        if(count($billPeriods)<=0)
        {
            return "{$year}年{$month}月 往后未找到账期";
        }
        $bill_period_ids = [];
        $bill_period_months = [];
        foreach ($billPeriods as $period)
        {
            $bill_period_ids[] = $period->id;
            $bill_period_months[] = 100*intval(date('Y', strtotime($period->month))) + intval(date('m', strtotime($period->month)));
        }

        //供应商数据
        $suppliers = \App\Models\Supplier::all();

        $count = [
            'init' => 0,
            'bill_pay' => 0,
            'payment_detail'=>0,
            'invoice_payment'=>0,
        ];

        foreach ($suppliers as $supplier)
        {
            ## 应付款初始余额
            // 同步期初数据
            // - 按照指定的年月获取供应商的期初数据
            // - 期初数据来源: 付款计划的 期初总余额
            $schedule = \App\Models\PaymentSchedule::query()
                    ->where('supplier_id', $supplier->id)
                    ->where('bill_period_id', $billPeriod->id)
                    ->first();

            if(!empty($schedule))
            {
               $res = \App\Models\SupplierBalanceFlow::syncInitByPaymentSchedule($schedule, false, true);
//               // 若初始化失败，则不同步后期数据
//               if(!$res)
//               {
//                   continue;
//               }
                $count['init'] = $count['init'] + $res?1:0;
            }

            ## 应付款增项
            $str = "''";
            if(count($bill_period_months)>0)
            {
                $str = join(',', $bill_period_months);
            }
            // 同步发票数据
            $invoices = \App\Models\InvoicePayment::query()
                    ->where('supplier_id', $supplier->id)
                    ->whereRaw("(100*year + month) IN ({$str})")
                    ->get();
            foreach ($invoices as $invoice)
            {
                $count['invoice_payment'] =  $count['invoice_payment'] + $invoice->save();
            }

            ## 应付款余额
            // 同步付款数据
            $bill_pays = \App\Models\BillPay::query()
                ->where('supplier_id', $supplier->id)
                ->whereIn('bill_period_id', $bill_period_ids)
                ->get();
            foreach ($bill_pays as $pay)
            {
                $count['bill_pay'] =  $count['bill_pay'] + $pay->save();
            }
            $payment_details = \App\Models\PaymentDetail::query()
                ->where('supplier_id', $supplier->id)
                ->whereIn('bill_period_id', $bill_period_ids)
                ->get();
            foreach ($payment_details as $detail)
            {
                $count['payment_detail'] =  $count['payment_detail'] + $detail->save();
            }
        }

        $strBillPeriod = join(',',  array_column( $billPeriods->toArray(), 'month'));
        return "更新完毕, 涉及供应商:{$suppliers->count()}, 账期:[{$strBillPeriod}],期初：{$count['init']}, 期内付款：{$count['bill_pay']},计划付款:{$count['payment_detail']}, 应付款发票：{$count['invoice_payment']}";

    }else{
        return "账户名或密码不正确";
    }
});

/**
 * 补全供应商基础信息
 * - 按照指定的账期-付款计划数据，来生成
 */
Route::get('backend/bill/{id}/supplier/completion/{account}/{password}'
    , function($id, $account, $password){
    // 验证账户密码
    if($account =='admin' && $password =='admin123')
    {
        $billPeriod = \App\Models\BillPeriod::query()->find($id);

        /**
         * @var $billPeriod \App\Models\BillPeriod
         */
        if(empty($billPeriod))
        {
            return '未找到指定账期';
        }

        // 获取账期供应商信息
        $schedules = $billPeriod->payment_schedules()->get();

        $month = intval(date('m', strtotime($billPeriod->month)));

        $count = 0;

        foreach ($schedules as $schedule)
        {
            /**
             * @var $supplier \App\Models\Supplier
             */
            $supplier = $schedule->supplier;

            if(empty($supplier))
            {
                continue;
            }
            // 识别码
            if(empty($supplier->code))
            {
                $supplier->code = $schedule->name;
            }

            //付款类型
            if(empty($supplier->payment_type_id))
            {
                $supplier->payment_type_id =  $schedule->payment_type_id;
            }

            //物料编码
            if(empty($supplier->payment_materiel_id))
            {
                $supplier->payment_materiel_id =  $schedule->payment_materiel_id;
            }

            //付款负责人
            if(empty($supplier->charge_man))
            {
                $supplier->charge_man =  $schedule->charge_man;
            }

            //pay_cycle_month
            if(empty($supplier->months_pay_cycle))
            {
                $num = $schedule->pay_cycle_month;

                $supplier->months_pay_cycle =  ($num>$month?(12+$month-$num):($month-$num)) - 1;
            }

            $flag = $supplier->save();

            if($flag)
            {
                $count++;
            }
        }

        return "更新账期{$billPeriod->name}, 计划数{$schedules->count()},供应商数{$count}";
    }
    else{
        return '身份验证失败';
    }
});