<script>
    $(function(){
        $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass   : 'iradio_minimal-blue'
        });

        $('#refreshInvoiceGather').click(function(){
            var url = "{{$url}}",
                  id =  '{{$billPeriod->id}}';
            swal({
                    title: "按月合计供应商发票金额",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{trans('admin.confirm')}}",
                    closeOnConfirm: false,
                    cancelButtonText: "{{trans('admin.cancel')}}"
                },
                function(){
                    $.ajax({
                        method: 'post',
                        url: url,
                        data: {
                            _method:'post',
                            _token:LA.token,
                            id:id
                        },
                        success: function (data) {
                            $.pjax.reload('#pjax-container');

                            if (typeof data === 'object') {
                                if (data.status) {
                                    swal(data.message, '', 'success');
                                } else {
                                    swal(data.message, '', 'error');
                                }
                            }
                        }
                    });
                });
        });
    });
</script>
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
                            已关联(供应商): {{ $payment_type->suppliers_count }}
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
<div>
   <h4> 筛选需要生成计划的类型</h4>
    <button id="refreshInvoiceGather" class="btn  btn-primary ">刷新并统计：上月({{($month==1)?12:($month-1)}}月)的应付款发票</button>
</div>
