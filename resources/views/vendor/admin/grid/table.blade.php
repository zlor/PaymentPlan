{{--// 中转多种样式--}}
@if(isset($useLayUI))
    @include("vendor.admin.grid.table_layui")
@else
    @include("vendor.admin.grid.table_fixed")
@endif