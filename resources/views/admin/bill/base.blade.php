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
        font-size: 15px;
    }
    .bill-period .list-group-item.right-item label{
        font-size: 13px;
        float: left;
        color:gray;
    }
    .bill-period .list-group-item.right-item.sub-sumer{
        border-top:1px solid #efefef;
    }
    .text-money {
        font-size:16px;
        font-weight: 600;
    }


</style>
<div class="callout callout-white panel-default bill-period">
    <div>
        <p><h4><i class="sum-span">期初余额:</i></h4>
            <ul class="list-group">
                <li class="list-group-item right-item">
                    <label>{{ __('bill.period.cash_balance') }}:</label>
                    <span class="text-money">{{ number_format($billPeriod->cash_balance, 2) }}</span>
                </li>
                <li class="list-group-item right-item">
                    <label>{{ __('bill.period.loan_balance') }}:</label>
                    <span class="text-money">{{ number_format($billPeriod->loan_balance, 2) }}</span>
                </li>
                <li class="list-group-item right-item">
                    <label>{{ __('bill.period.acceptance_line') }}:</label>
                    <span class="text-money">{{ number_format($billPeriod->acceptance_line, 2) }}</span>
                </li>
                <li class="list-group-item right-item">
                    <label>{{ __('bill.period.invoice_balance') }}:</label>
                    <span class="text-money">{{ number_format($billPeriod->invoice_balance, 2) }}</span>
                </li>
                <li class="list-group-item right-item sub-sumer">
                    <label>合计:</label>
                    <span class="text-money">{{ number_format($billPeriod->init_total, 2) }}</span>
                </li>
            </ul>
        </p>
    </div>
    <div>
        <p>
        <h4><i class="sum-span">已付款:</i></h4>
        <ul class="list-group">
            <li class="list-group-item right-item">
                <label>现金:</label>
                <span class="text-money">{{ number_format($billPeriod->cash_paid,2) }}</span>
            </li>
            <li class="list-group-item right-item">
                <label>承兑:</label>
                <span class="text-money">{{ number_format($billPeriod->acceptance_paid,2) }}</span>
            </li>
            <li class="list-group-item right-item sub-sumer">
                <label>合计:</label>
                <span class="text-money">{{ number_format($billPeriod->paid_total, 2) }}</span>
            </li>
        </ul>
        </p>
    </div>
    <div>
        <p>
        <h4><i class="sum-span">已收款:</i></h4>
        <ul class="list-group">
            <li class="list-group-item right-item">
                <label>现金:</label>
                <span class="text-money">{{ number_format($billPeriod->cash_collected,2) }}</span>
            </li>
            <li class="list-group-item right-item">
                <label>承兑:</label>
                <span class="text-money">{{ number_format($billPeriod->acceptance_collected,2) }}</span>
            </li>
            <li class="list-group-item right-item sub-sumer">
                <label>合计:</label>
                <span class="text-money">{{ number_format($billPeriod->collected_total, 2) }}</span>
            </li>
        </ul>
        </p>
    </div>
    <div>
        <p>
            <h4><i class="sum-span">当前余额:</i></h4>
            <ul class="list-group">
                <li class="list-group-item right-item">
                    <label>现金:</label>
                    <span class="text-money">{{ number_format($billPeriod->current_cash_balance, 2) }}</span>
                </li>
                <li class="list-group-item right-item">
                    <label>承兑:</label>
                    <span class="text-money">{{ number_format($billPeriod->current_acceptance_balance, 2) }}</span>
                </li>
                <li class="list-group-item right-item sub-sumer">
                    <label>合计:</label>
                    <span class="text-money">{{ number_format($billPeriod->balance, 2) }}</span>
                </li>
            </ul>
        </p>
    </div>
    <hr>
    <div>
        <p>
        <h4>应付供应商:  </h4>
        <ul class="list-group">
            <li class="list-group-item right-item">
                <label>合计:</label>
                <span class="text-number">{{ $billPeriod->countSuppliers()}}</span></li>
        </ul>
        </p>
    </div>
    <div>
        <p>
        <h4><i class="sum-span">应付款:</i></h4>
        <ul class="list-group">
            <li class="list-group-item right-item">
                <label>合计:</label>
                <span class="text-money">{{ number_format($billPeriod->current_due_money, 2) }}</span>
            </li>
        </ul>
        </p>
    </div>
    {{--<h5>--}}

        {{--<br>--}}
        {{--余/现金总额 :   <span>{{$billPeriod->current_cash_balance }} / {{$billPeriod->cash_balance }}</span>--}}
        {{--<br>--}}
        {{--余/承兑总额 :   <span>{{$billPeriod->current_acceptance_balance }} / {{$billPeriod->acceptance_line}}</span>--}}
        {{--<br>--}}
        {{--余/现金池: <span>{{$billPeriod->balance}} / {{$billPeriod->cash_pool}} </span>--}}
    {{--</h5>--}}
</div>