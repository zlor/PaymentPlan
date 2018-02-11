<style>
    tr.focus{
        background: #efefef;
    }
    .bill-period .right-item{
        width: 10em;
        text-align: right;
        float: right;
        margin-left:3em;
    }
    .bill-period .right-item label{
        float: left;
    }

</style>
<h4>概要信息:</h4>
<div class="callout callout-success panel-default bill-period">

    <h4>
        <span>期初余额: {{ $billPeriod->init_total }}</span>
        <span class="pull-right">
            <div class="right-item"><label>现金:</label> {{ number_format($billPeriod->cash_balance, 2) }} </div>
            <div class="right-item"><label>承兑:</label> {{ number_format($billPeriod->acceptance_line, 2) }} </div>
        </span>
    </h4>
    <h4>
        <span>总应付款: {{ $billPeriod->current_due_money}} </span>
        <span class="pull-right">
        </span>
    </h4>

    <hr>

    <h4>
        <span>当前余额: {{ $billPeriod->balance }}</span>
        <span class="pull-right">
            <div class="right-item"><label>现金:</label> {{ number_format($billPeriod->current_cash_balance, 2) }}</div>
            <div class="right-item"><label>承兑:</label> {{ number_format($billPeriod->current_acceptance_balance, 2) }} </div>
        </span>
    </h4>
    <h4>
        <span>总已付款: {{ $billPeriod->paid_total }} </span>
        <span class="pull-right">
            <div class="right-item"><label>现金:</label> {{ number_format($billPeriod->cash_paid,2) }}</div>
            <div class="right-item"><label>承兑:</label>{{ number_format($billPeriod->acceptance_paid,2) }} </div>
        </span>
    </h4>
    <hr>
    <h4>
        <span>应付供应商: {{ $billPeriod->countSuppliers()}} </span>
        <span class="pull-right">
        </span>
    </h4>
    <h5>

        <br>
        余/现金总额 :   <span>{{$billPeriod->current_cash_balance }} / {{$billPeriod->cash_balance }}</span>
        <br>
        余/承兑总额 :   <span>{{$billPeriod->current_acceptance_balance }} / {{$billPeriod->acceptance_line}}</span>
        <br>
        余/现金池: <span>{{$billPeriod->balance}} / {{$billPeriod->cash_pool}} </span>
    </h5>
</div>