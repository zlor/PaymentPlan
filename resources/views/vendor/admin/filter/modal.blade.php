<style>
    .custom-filter {
        box-shadow: none;
        margin-bottom: 10px;
        border: 1px solid #eee;
    }

    .custom-filter.collapsed-box {
        border: 0;
    }

    .custom-filter .pull-right {
        position: absolute;
        right: 0;
        z-index: 99;
    }

    .custom-filter.collapsed-box .pull-right {
        position: relative;
    }

    .custom-filter .form-group:last-child {
        margin-bottom: 0;
    }
</style>
<div class="box custom-filter">
    <div class="box-tools pull-right">
        <button class="btn btn-box-tool" data-widget="collapse">筛选<i class="fa fa-minus"></i></button>
    </div>
    <form action="{!! $action !!}" method="get" pjax-container>
        <div class="box-body">
            <div class="form">
                @foreach($filters as $filter)
                    <div class="form-group col-sm-2">
                        {!! $filter->render() !!}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-primary submit"
                    style="float: right;">{{ trans('admin.filter') }}</button>
            <a href="{!! $action !!}" class="btn btn-primary btn-facebook"
               style="float: right; margin-right: 10px;">{{ trans('admin.reset') }}</a>
        </div>
    </form>
</div>
{{--<div class="with-border" style="">--}}
    {{--<div class="form-inline pull-right">--}}
        {{--<form action="{!! $action !!}" method="get" pjax-container>--}}
            {{--<fieldset>--}}

                {{--@foreach($filters as $filter)--}}
                    {{--{!! $filter->render() !!}--}}
                {{--@endforeach--}}

                {{--<div class="btn-group btn-group-sm pull-right">--}}
                    {{--<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>--}}
                    {{--<a href="{!! $action !!}" class="btn btn-warning" ><i class="fa fa-undo"></i></a>--}}
                {{--</div>--}}

            {{--</fieldset>--}}
        {{--</form>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<div class="box no-margin">--}}
    {{--<div class="box-header">--}}
        {{--<h4 class="box-title" id="myModalLabel">{{ trans('admin.filter') }}</h4>--}}
        {{--<div class="box-tools pull-right">--}}
            {{--<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>--}}
            {{--</button>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="box-body with-border">--}}
        {{--<div class=" form-inline pull-right">--}}
            {{--<form action="{!! $action !!}" method="get" pjax-container>--}}
                {{--<fieldset>--}}

                    {{--@foreach($filters as $filter)--}}
                        {{--{!! $filter->render() !!}--}}
                    {{--@endforeach--}}

                    {{--<div class="btn-group btn-group-sm pull-right">--}}
                        {{--<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>--}}
                        {{--<a href="{!! $action !!}" class="btn btn-warning" ><i class="fa fa-undo"></i></a>--}}
                    {{--</div>--}}

                {{--</fieldset>--}}
            {{--</form>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<form action="{!! $action !!}" method="get" pjax-container>--}}
    {{--<div class="box-body">--}}
        {{--<div class="form">--}}
            {{--@foreach($filters as $filter)--}}
                {{--<div class="form-group">--}}
                    {{--{!! $filter->render() !!}--}}
                {{--</div>--}}
            {{--@endforeach--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="box-footer pull-right">--}}
        {{--<button type="submit" class="btn btn-primary submit">{{ trans('admin.submit') }}</button>--}}
        {{--<button type="reset" class="btn btn-warning pull-left">{{ trans('admin.reset') }}</button>--}}
    {{--</div>--}}
{{--</form>--}}