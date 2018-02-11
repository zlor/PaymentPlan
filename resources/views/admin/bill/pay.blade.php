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
    <div class="pull-right" style="width: 15em">
        <div class="pull-left" style="font-size: 18px; margin-right:0">资金</div>
        <fieldset>
            <p>
            <ul>
                <li>现金:<span> + {{$bill_period->current_cash_balance }}</span></li>
                <hr style="margin: 0">
                <li>额度:<span> + {{$bill_period->quota_total }}</span></li>
            </ul>
            </p>
        </fieldset>
    </div>
    <div class="pull-right" style="width: 15em">
        <div class="pull-left" style="font-size: 18px; margin-right:0"> + 收款
        </div>
        <fieldset>
            <p>
            <ul>
                <li>现金:<span> + {{$bill_period->cash_collected }}</span></li>
                <hr style="margin: 0">
                <li>承兑:<span> + {{$bill_period->acceptance_collected }}</span></li>
            </ul>
            </p>
        </fieldset>
    </div>
    <div class="pull-right" style="width: 15em">
        <div class="pull-left" style="font-size: 18px; margin-right:0"> - 支付
        </div>
        <fieldset>
            <p>
            <ul>
                <li>现金:<span> - {{ $bill_period->cash_paid }}</span></li>
                <hr style="margin: 0">
                <li>承兑:<span> - {{ $bill_period->acceptance_paid }}</span></li>
            </ul>
            </p>
        </fieldset>
    </div>
    <div class="pull-right" style="width: 15em">
        <div class="pull-left" style="font-size: 18px; margin-right:0"> 期初  </div>
        <fieldset>
            <p>
                <ul>
                    <li>现金: {{$bill_period->cash_balance }}</li>
                    <li>贷款: {{$bill_period->loan_balance }}</li>
                <hr style="margin: 0">
                    <li>承兑: {{$bill_period->acceptance_line }}</li>
                    <li>来款: {{$bill_period->invoice_balance }}</li>
                </ul>
            </p>
        </fieldset>
    </div>
    @if(isset($paymentSchedule))
        <p>
            <h5>应付款: </h5>
            <ul>
                <li class="list-unstyle"><span>{{ $paymentSchedule->due_money }}</span></li>
            </ul>
        </p>
        <p>
            <h5>已付款</h5>
            <ul>
                <li><span>现金: {{ $paymentSchedule->cash_paid }}</span></li>
                <li><span>承兑: {{ $paymentSchedule->acceptance_paid }}</span></li>
            </ul>
        </p>
    @else
        <p>
            <h5>已付款(其他): {{ $bill_period_other['paid_money']}}  </h5>
            <ul>
                <li><span>现金: {{ $bill_period_other['cash_paid'] }}</span></li>
                <li><span>承兑: {{ $bill_period_other['acceptance_paid'] }}</span></li>
            </ul>
        </p>
    @endif
</div>