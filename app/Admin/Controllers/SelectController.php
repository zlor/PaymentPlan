<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Input;

class SelectController extends Controller
{

    public function paymentScheduleLoading()
    {
        $params = Input::all();

        $billPeriodId = intval($params['q']);

        $billPeriod = BillPeriod::query()->first($billPeriodId);

        if(empty($billPeriod))
        {
            $options = [];

        }else{

            $schedules = PaymentSchedule::query()->where('bill_period_id', $billPeriodId)->get();

            $options = $schedules->pluck('select_text', 'id');

            dd($options);
        }

        return response()->json($options);
    }
}
