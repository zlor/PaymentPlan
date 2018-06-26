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
Route::get('buildSehedule/from/{fromId}/to/{toId}/{diffMonth}', function($fromId, $toId, $diffMonth){
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