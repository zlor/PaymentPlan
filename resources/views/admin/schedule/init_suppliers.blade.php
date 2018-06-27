{{-- 暂时所有的 payment types --}}
<div class="">
    <ul>
        @foreach($suppliers as $supplier)
            @if($supplier->balanceMoney > 0 )
                <li>
                    <span>{{$supplier->name}}</span>
                    <span>[应付款余额：{{$supplier->balanceMoney}}]</span>
                    @if(empty($supplier->code) || empty($supplier->chargeMan))
                    <a class="btn btn-warning btn-xs" href="/admin/base/suppliers/{{$supplier->id}}/edit"  target="_blank">补全信息</a>
                    @endif
                </li>
                @endif
        @endforeach

    </ul>
</div>
