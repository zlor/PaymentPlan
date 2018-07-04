<span>
    <div class="text-right" title="实付">{{number_format($value,2)}}</div>
    @if(isset($reportMap[$key]))
        <hr class="no-margin">
        <div class="text-right" title="建议应付">{{ number_format($reportMap[$key]['suggest_due_money'],2)}}</div>
        <hr class="no-margin">
        <div class="text-right" title="月初应付">{{number_format($reportMap[$key]['supplier_balance'], 2)}}</div>
    @endif
</span>