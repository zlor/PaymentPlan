<style>
    tr.focus{
        background: #efefef;
    }
    .bill-period{
        min-width: 1000px;
    }
    .sum-span{
        color:gray;
    }
    .bill-period .list-group-item.right-item{
        text-align: right;
        border: 0;
        background:none;
        width: 14em;
        padding: .2em;
    }
    .bill-period .list-group-item.right-item label{
        font-size: 13px;
        float: left;
        color:gray;
    }


</style>
<div class="callout callout-white panel-default bill-period">
    <h4>
        <p>
            <span><i class="sum-span">期初余额:</i> {{ number_format($billPeriod->cash_pool, 2) }}</span>
            <ul class="list-group">
                <li class="list-group-item right-item"><label>{{ __('bill.period.cash_balance') }}:</label> {{ number_format($billPeriod->cash_balance, 2) }} </li>
                <li class="list-group-item right-item"><label>{{ __('bill.period.invoice_balance') }}:</label> {{ number_format($billPeriod->invoice_balance, 2) }} </li>
                <li class="list-group-item right-item"><label>{{ __('bill.period.acceptance_line') }}:</label> {{ number_format($billPeriod->acceptance_line, 2) }} </li>
                <li class="list-group-item right-item"><label>{{ __('bill.period.loan_balance') }}:</label> {{ number_format($billPeriod->loan_balance, 2) }} </li>
            </ul>
        </p>
    </h4>
    <h4>
        <span><i class="sum-span">总应付款:</i> {{ number_format($billPeriod->current_due_money, 2)}} </span>
        <span class="pull-right">
        </span>
    </h4>

    <hr>

    <h4>
        <p>
            <span><i class="sum-span">当前余额:</i> {{ number_format($billPeriod->balance, 2)}}</span>
            <ul class="list-group">
                <li class="list-group-item right-item"><label>现金:</label> {{ number_format($billPeriod->current_cash_balance, 2) }}</li>
                <li class="list-group-item right-item"><label>承兑:</label> {{ number_format($billPeriod->current_acceptance_balance, 2) }} </li>
            </ul>
        </p>
    </h4>
    <h4>
        <p>
            <span><i class="sum-span">总已付款:</i> {{ number_format($billPeriod->paid_total, 2)}} </span>
            <ul class="list-group">
                <li class="list-group-item right-item"><label>现金:</label> {{ number_format($billPeriod->cash_paid,2) }}</li>
                <li class="list-group-item right-item"><label>承兑:</label>{{ number_format($billPeriod->acceptance_paid,2) }} </li>
            </ul>
        </p>
    </h4>
    <hr>
    <h4>
        <span>应付供应商: {{ $billPeriod->countSuppliers()}} </span>
        <span class="pull-right">
        </span>
    </h4>
    {{--<h5>--}}

        {{--<br>--}}
        {{--余/现金总额 :   <span>{{$billPeriod->current_cash_balance }} / {{$billPeriod->cash_balance }}</span>--}}
        {{--<br>--}}
        {{--余/承兑总额 :   <span>{{$billPeriod->current_acceptance_balance }} / {{$billPeriod->acceptance_line}}</span>--}}
        {{--<br>--}}
        {{--余/现金池: <span>{{$billPeriod->balance}} / {{$billPeriod->cash_pool}} </span>--}}
    {{--</h5>--}}
</div>