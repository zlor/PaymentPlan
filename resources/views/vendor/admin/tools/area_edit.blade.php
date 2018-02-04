{{--<div class="btn-group" data-toggle="buttons">--}}
    {{--@foreach($options as $option => $label)--}}
        {{--<label class="btn btn-default btn-sm {{ \Request::get('gender', 'all') == $option ? 'active' : '' }}">--}}
            {{--<input type="radio" class="user-gender" value="{{ $option }}">{{$label}}--}}
        {{--</label>--}}
    {{--@endforeach--}}
{{--</div>--}}


<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default btn-area-edit" ><i class="fa fa-edit"></i> 编辑 </a>
    {{--<button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown">--}}
        {{--<span class="caret"></span>--}}
        {{--<span class="sr-only">Toggle Dropdown</span>--}}
    {{--</button>--}}
    {{--<ul class="dropdown-menu" role="menu">--}}
        {{--<li><a href="/admin/plan/schedule?id=sdfsdf&amp;name=sd&amp;gender=all&amp;_export_=all" target="_blank">全部</a></li>--}}
        {{--<li><a href="/admin/plan/schedule?id=sdfsdf&amp;name=sd&amp;gender=all&amp;_export_=page%3A1" target="_blank">当前页</a></li>--}}
        {{--<li><a href="/admin/plan/schedule?id=sdfsdf&amp;name=sd&amp;gender=all&amp;_export_=selected%3A__rows__" target="_blank" class="export-selected">选择的行</a></li>--}}
    {{--</ul>--}}
</div>
