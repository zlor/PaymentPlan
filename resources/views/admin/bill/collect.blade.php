<style>
    tr.focus{
        background: #efefef;
    }
</style>

<h4>
    <span>账期: {{ $bill_period->name }} </span>
</h4>
<div class="callout callout-success">
    <div class="pull-right" style="width: 25em">
        <div class="pull-left" style="font-size: 18px; margin-right:1.5em"> 本期现金池  </div>
        <fieldset>
            余/现金总额 :   <span>{{$bill_period->current_cash_balance }} / {{$bill_period->cash_balance }}</span>
            <br>
            余/承兑总额 :   <span>{{$bill_period->current_acceptance_balance }} / {{$bill_period->acceptance_line}}</span>
            <br>
            余/现金池: <span>{{$bill_period->balance}} / {{$bill_period->cash_pool}} </span>
        </fieldset>
    </div>

    <h4>
        <span>已收款(其他): {{ $bill_period_other['collected_money']}}  </span>

    </h4>
    <h4>
        <span style="margin-left:2em">现金: {{ $bill_period_other['cash_collected'] }},</span>
        <span style="margin-left:2em">承兑: {{ $bill_period_other['acceptance_collected'] }}</span>
    </h4>
</div>