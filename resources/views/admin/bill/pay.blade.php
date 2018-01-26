<style>
    tr.focus{
        background: #efefef;
    }
</style>

<h4>
    <span>账期: {{ $paymentSchedule->bill_period_name }} </span>
    <span style="margin-left:2em">类型: {{ $paymentSchedule->payment_type_name }} </span>
    <span style="margin-left:2em">物料: {{ $paymentSchedule->payment_materiel_name }} </span>
    <span style="margin-left:2em">供应商: {{ $paymentSchedule->supplier_name }}</span>
    <span style="margin-left:2em">计划编号：{{ $paymentSchedule->id }} </span>
</h4>
<div class="callout callout-success">
    <div class="pull-right" style="width: 25em">
        <div class="pull-left" style="font-size: 18px; margin-right:1.5em"> 本期现金池  </div>
        <fieldset>
            余/现金总额 :   <span>{{$paymentSchedule->bill_period->current_cash_balance }} / {{$paymentSchedule->bill_period->cash_balance }}</span>
            <br>
            余/承兑总额 :   <span>{{$paymentSchedule->bill_period->current_acceptance_balance }} / {{$paymentSchedule->bill_period->acceptance_line}}</span>
            <br>
            余/现金池: <span>{{$paymentSchedule->bill_period->balance}} / {{$paymentSchedule->bill_period->cash_pool}} </span>
        </fieldset>
    </div>

    <h4>
        <span>应付款: {{ $paymentSchedule->due_money }} </span>
    </h4>
    <h4>
        <span>已付款: {{ $paymentSchedule->paid_money }} [现金: {{ $paymentSchedule->cash_paid }},承兑: {{ $paymentSchedule->acceptance_paid }}] </span>

        <span style="margin-left:2em"></span>
        <span style="margin-left:2em"></span>
    </h4>
</div>