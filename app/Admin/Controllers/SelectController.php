<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentMateriel;
use App\Models\PaymentSchedule;
use App\Models\Supplier;
use Illuminate\Support\Facades\Input;

class SelectController extends Controller
{

    public function paymentScheduleLoading()
    {
        $params = Input::all();

        $billPeriodId = intval($params['q']);

        $billPeriod = BillPeriod::query()->first($billPeriodId);

        if (empty($billPeriod)) {
            $options = [];

        } else {

            $schedules = PaymentSchedule::query()->where('bill_period_id', $billPeriodId)->get();

            $options = $schedules->pluck('select_text', 'id');

            dd($options);
        }

        return response()->json($options);
    }

    public function paymentMaterielOptions()
    {
        $params = Input::all();

        $returnOptions = intval(isset($params['returnOptions'])?$params['returnOptions']:0)>0;

        $materiels = PaymentMateriel::all();

        $result = [];

        $result['status'] = 'succ';

        if($returnOptions)
        {
            $options = $materiels->pluck('name', 'id');

            $result['options'] = $options;
        }else{

            $result['options'] = $materiels->toArray();
        }

        return response()->json($result);
    }

    public function paymentSupplierOptions()
    {
        $params = Input::all();

        $returnOptions = intval(isset($params['returnOptions'])?$params['returnOptions']:0)>0;

        $suppliers = Supplier::all();

        $result = [];

        $result['status'] = 'succ';

        if($returnOptions)
        {
            $options = $suppliers->pluck('name', 'id');

            $result['options'] = $options;
        }else{

            $result['options'] = $suppliers->toArray();
        }

        return response()->json($result);
    }
}
