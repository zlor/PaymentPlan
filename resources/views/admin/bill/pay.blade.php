<style>
    tr.focus{
        background: #efefef;
    }
</style>

<h4>
    <span>账期: {{ $bill_period->name }} </span>
    @if(isset($paymentSchedule))
        <span style="margin-left:2em">类型: {{ $paymentSchedule->payment_type_name }} </span>
        <span style="margin-left:2em">物料: {{ $paymentSchedule->payment_materiel_name }} </span>
        <span style="margin-left:2em">供应商: {{ $paymentSchedule->supplier_name }}</span>
        <span style="margin-left:2em">计划编号：{{ $paymentSchedule->id }} </span>
    @endif
</h4>
<div class="callout callout-success">
    <div class="pull-right" style="width: 25em">
        <div class="pull-left" style="font-size: 18px; margin-right:1.5em"> 本期资金池  </div>
        <fieldset>
            现金（ <span> 余 {{$bill_period->current_cash_balance }}</span>  <i>|</i> <span> 总额 {{$bill_period->cash_balance }}</span>）
            <br>
            承兑（ <span> 余 {{$bill_period->current_acceptance_balance }}</span> <i>|</i> <span>总额 {{$bill_period->acceptance_line}}</span>）
            <br>
            资金（ <span> 余 {{$bill_period->balance}}</span>  <i>|</i> <span> 总额 {{$bill_period->cash_pool}} </span>）
        </fieldset>
    </div>
    @if(isset($paymentSchedule))
        <h4>
            <span>应付款: {{ $paymentSchedule->due_money }} </span>
        </h4>
        <h4>
            <span>已付款: {{ $paymentSchedule->paid_money }} [现金: {{ $paymentSchedule->cash_paid }},承兑: {{ $paymentSchedule->acceptance_paid }}] </span>

            <span style="margin-left:2em"></span>
            <span style="margin-left:2em"></span>
        </h4>
    @else
        <h4>
            <span>已付款(其他): {{ $bill_period_other['paid_money']}}  </span>

        </h4>
        <h4>
            <span style="margin-left:2em">现金: {{ $bill_period_other['cash_paid'] }},</span>
            <span style="margin-left:2em">承兑: {{ $bill_period_other['acceptance_paid'] }}</span>
        </h4>

    @endif
</div>