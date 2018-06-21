{{--<script type="text/javascript">--}}
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