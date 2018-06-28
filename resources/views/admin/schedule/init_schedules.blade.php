<script type="text/javascript">
    $(function(){
        $('#initInvoiceGather').click(function(){
            var url = "{{$url}}",
                id =  '{{$billPeriod->id}}';
            swal({
                    title: '{{$billPeriod->name}},生成计划',
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
<div class="">
    <div>
        <button id="initInvoiceGather" class="btn btn-primary">生成计划</button>
    </div>
</div>
