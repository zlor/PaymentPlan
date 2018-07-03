@if( session('session_flash')['useSinglePage'] )
    @include('admin::indexSinglePage')
@elseif( session('session_flash')['useReportPage'])
    @include('admin.indexReportPage')
@else
    @include('admin::indexAllPage')
@endif