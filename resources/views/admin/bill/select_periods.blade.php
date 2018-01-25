{{--<select class="form-control form-inline" name="default_bill_period_id" id="default_bill_period_id">--}}
    {{--@foreach($options as $option)--}}
        {{--<option value="{{ $option['value'] }}" @if($option['selected']) selected="selected" @endif>{{ $option['text'] }}</option>--}}
    {{--@endforeach--}}
{{--</select>--}}
<div class="buttons">
        <input type="hidden" name="default_bill_period_id" id="default_bill_period_id" value="{{ $defaultPeriodId or 0 }}">
        @foreach($options as $option)
                <a href="{{ $option['url']  }}" class="btn btn-default @if($option['selected']) btn-success active @endif">{{ $option['text'] }}</a>
        @endforeach
</div>

{{--<div class="btn-group">--}}
    {{--<button type="button" class="btn btn-success">Action</button>--}}
    {{--<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">--}}
        {{--<span class="caret"></span>--}}
        {{--<span class="sr-only">Toggle Dropdown</span>--}}
    {{--</button>--}}
    {{--<ul class="dropdown-menu" role="menu">--}}
        {{--<li><a href="#">Action</a></li>--}}
        {{--<li><a href="#">Another action</a></li>--}}
        {{--<li><a href="#">Something else here</a></li>--}}
        {{--<li class="divider"></li>--}}
        {{--<li><a href="#">Separated link</a></li>--}}
    {{--</ul>--}}
{{--</div>--}}