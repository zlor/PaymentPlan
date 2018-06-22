@if( session('session_flash')['useSinglePage'] )
    @include('admin::indexSinglePage')
@else
    @include('admin::indexAllPage')
@endif