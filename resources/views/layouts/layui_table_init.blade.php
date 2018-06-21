$(function(){
    var targetTableId = "data-table";
    var max_width = 1000;
    $("#data-table").attr("lay-filter", "lay-table");
   max_width = $(".box").width()>0?$("#data-div").width():max_width;

    // 转化为 layui-table
    layui.use('table', function(){
         var table = layui.table;

        //转换静态表格
        table.init("lay-table", {
                height: 315 //设置高度
        });
});
});
