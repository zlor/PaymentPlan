<script>
    $(function(){
        $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass   : 'iradio_minimal-blue'
        });
    });
</script>
<div class="h5"> 筛选需要生成计划的类型 </div>
{{-- 暂时所有的 payment types --}}
<div class="row">
    @foreach($payment_types as $payment_type)
        <div class="col-md-2 col-sm-4 col-xs-6">
            @if($payment_type->is_plan)
                @if(!$payment_type->is_closed)
                    <label class="info-box" for="checkbox_{{$payment_type->id}}" style="cursor:pointer;">
                        <p>
                            <input type="checkbox" id="checkbox_{{$payment_type->id}}" class="minimal" checked="checked">
                            <span class="text-blue">{{ $payment_type->name }}</span>
                        </p>
                        <p>
                            {{--描述文字，展示类型相关的信息，涉及多少--}}
                        </p>
                    </label>
                @else
                    <lebel class="info-box" for="checkbox_{{$payment_type->id}}">
                        <input type="checkbox"  id="checkbox_{{$payment_type->id}}" class="minimal" disabled="disabled">
                        <span class="text-gray">{{ $payment_type->name }}</span>
                    </lebel>
                @endif
            @else
                    <lebel class="text-gray"title="不在计划内">{{ $payment_type->name }}</lebel>
            @endif
        </div>
    @endforeach
</div>
