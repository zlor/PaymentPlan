{{-- 暂时所有的 payment types --}}
<div class="">
    <table class="table">
        <thead>
            <tr>
                <th>供应商名称</th>
                <th>{{($month==1)?12:($month-1)}}月 应付款发票</th>
                <th>应付款余额</th>
                <th>必要信息 (需要补全标黑的信息)</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        @foreach($suppliers as $supplier)
            @if($balanceSuppliers[$supplier->id] > 0 )
                <tr>
                    <td>{{$supplier->name}}</td>
                    <td>{{$invoiceGathers[$supplier->id]}}</td>
                    <td>{{$balanceSuppliers[$supplier->id]}}</td>
                    <td>
                        @if(empty($supplier->payment_type_id))
                            【付款方式】
                        @else
                            <span class="text-gray">【{{$supplier->payment_type->name}}】</span>
                        @endif
                        @if(empty($supplier->code))
                           【科目编码】
                        @else
                            <span class="text-gray">【{{$supplier->code}}】</span>
                        @endif
                        @if(empty($supplier->charge_man))
                            【付款确认人】
                        @else
                            <span class="text-gray">【{{$supplier->charge_man}}】</span>
                        @endif
                        @if(empty($supplier->months_pay_cycle))
                            【付款周期】
                        @else
                            <span class="text-gray">【{{$supplier->months_pay_cycle  }}】</span>
                        @endif
                    </td>
                    <td>
                        @if(empty($supplier->code) || empty($supplier->charge_man) || empty($supplier->payment_type) || empty($supplier->months_pay_cycle))
                            <a class="btn btn-warning btn-xs" href="/admin/base/suppliers/{{$supplier->id}}/edit?useFast=1"  target="_blank">补全信息</a>
                        @endif
                    </td>
                </tr>
                @endif
        @endforeach
        </tbody>
    </table>
</div>
