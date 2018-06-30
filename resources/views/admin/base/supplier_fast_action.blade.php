{{--<script type="text/javascript">--}}
    {{--  增加供应商选中后的操作 --}}
    $(function(){
        var old_supplier_obj = {'value':$('select[name="supplier_id"]').val(), 'text':$('select[name="supplier_id"]').find('option:selected').text()};

        $('select[name="supplier_id"]').change(function(){
            var supplier_name = $(this).find('option:selected').text();
            if($('[name="supplier_name"]')
                && ( !$('[name="supplier_name"]').val() || old_supplier_obj.text == $('[name="supplier_name"]').val())
            ){
                $('[name="supplier_name"]').val(supplier_name);
            }
            old_supplier_obj = {'value':$(this).val(), 'text':supplier_name};

            // 获取供应商信息，填充表单
            var url = '{{$getSupplierOneUrl}}';
            $.get(url, {'id':$(this).val()}, function(data){
                if(data.status == 'succ')
                {
                    var supplier = data.result;
                    // 填充物料信息
                    if(supplier.payment_materiel_id>0)
                    {
                        $('[name="payment_materiel_id"]').val(supplier.payment_materiel_id).change();
                    }else{
                        $('[name="payment_materiel_id"]').val(0).change();
                    }

                    // 填充类型
                    if(supplier.payment_type_id>0)
                    {
                        $('[name="payment_type_id"]').val(supplier.payment_type_id).change();
                    }else{
                        $('[name="payment_type_id"]').val(0).change();
                    }
                    // 填充抬头
                    if(!$('#title').val()){
                        $('[name="title"]').val(supplier.name);
                    }
                    @if(isset($needRenderName) && $needRenderName)
                        $('[name="name"]').val(supplier.code);
                    @endif
                }
            }, 'json');
        });
    });


   {{-- 启用 layui 构建快速添加界面 --}}
    layui.use('layer', function(){
        var layer = layui.layer;

        $('#fastSupplierAction').click(function(){
            var url = $(this).data('url')
                ,reloadOptionsUrl = $(this).data('reloadoptionsurl')
                ,targetSelectName = $(this).data('targetname');

            layer.open({
                type: 2
                ,content:  url
                ,maxmin: true
                ,area: ['900px', '600px']
                // 销毁后回调
                ,end: function(index, layero){
                    $.get(reloadOptionsUrl, {'returnOptions':0}, function(response){
                        // 刷新供应商内容
                        $("select[name="+targetSelectName+"]").empty();

                        $("[name="+targetSelectName+"]").append("<option value=''>请选择</option>");
                        $(response.options).each(function(){
                            $("[name="+targetSelectName+"]").append("<option value='"+this.id+"'>"+this.name+"</option>");
                        });
                        $("select[name="+targetSelectName+"]").select2();
                    }, 'json')

                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

    });

{{--</script>--}}