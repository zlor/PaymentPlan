<script>
    $(function(){
        $('#initGatherBtn').click(function(){
            var url = "{{$url}}",
                id =  $("#initGatherBillPeriod").val();

            if(!id){
                swal("请选择账期", '', 'warning');
                return false;
            }
            swal({
                    title: "按账期初始化供应商的发票金额",
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
<div class="pull-right">
    <div class="form-group">
        <label class="sr-only" for="exampleInputAmount">初始化</label>
        <div class="input-group" style="width: 190px;font-size:1em;">
            <select  class="form-control" name="initGatherBillPeriod" id="initGatherBillPeriod">
                @foreach($billPeriods as $billPeriod)
                    <option value="{{$billPeriod->id}}">{{$billPeriod->name}}</option>
                @endforeach
            </select>
            <div class=" input-group-addon btn btn-primary " id="initGatherBtn">初始化</div>
        </div>
    </div>

</div>
