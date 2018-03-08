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

Route::get('getExcel/{id}', function($id){

    $billPeriod = \App\Models\BillPeriod::query()->findOrFail($id);

    $schedules = \App\Models\PaymentSchedule::query()->where('bill_period_id', $billPeriod->id)->get();
    $head = ["供应商名称", '类型', "总应付金额", "建议应付金额"];

    $sheetData[] = $head;

    foreach ($schedules as $schedule)
    {
        $row = [
            0=> $schedule->supplier_name,
            1=> $schedule->payment_type_name,
            2=> $schedule->supplier_balance,
            3=> $schedule->suggest_due_money,
        ];
        $sheetData[] = $row;
    }

    $sheetName = 'tmp';

    $excel = Excel::create('tmp', function($excel)use($sheetData, $sheetName){

        $excel->sheet($sheetName, function($sheet)use($sheetData) {

            $sheet->fromArray($sheetData);
        });
    })->store('xls', storage_path('public'))->download();
});
