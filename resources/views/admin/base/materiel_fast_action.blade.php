{{--<script type="text/javascript">--}}
$(function(){
    $('select[name="payment_materiel_id"]').change(function(){

        @if(isset($needRenderName) && $needRenderName)
            $('[name="materiel_name"]').val($(this).find('option:selected').text());
        @endif
    });
});
layui.use('layer', function(){
        var layer = layui.layer;

        $('#fastMaterielAction').click(function(){
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
                                    // 刷新物料内容
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