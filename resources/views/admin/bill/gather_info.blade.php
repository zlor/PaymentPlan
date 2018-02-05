<style>
    .period-info{
        min-height: 425px;
    }
    ul.clearfix{
        display: table;
        padding: 0;
        margin: auto;
    }
    .pay-li-min{
        float: left;
        text-align: center;
        overflow: hidden;
        width: 20%;
        min-width: 200px;
        height: 116px;
        border: 1px solid #E4E4E4;
        background: #FFFFFF;
        margin-right: -1px;
        margin-top: -1px;
        position: relative;
    }
    .pay-li-min:hover{
        background: #FFFFFF;
        border: 1px solid #38ADFF;
        box-shadow: 0 0 10px 0 rgba(56, 173, 255, 0.10);
        z-index: 1000;
        cursor: pointer;
    }
    .pay-li-min.active{
        background-color:#efefef;
        border: 1px solid #2ca02c;
        box-shadow: 0 0 10px 0 rgba(56, 173, 255, 0.10);
        z-index: 1000;
    }
    .pay-li-min .col-sm-4{
        margin: 0;
        padding: 0;
    }
    .pay-li-min .pay-item{
        position: relative;
        margin-top: -22px;
    }

    .pay-li-min .pay-item .pay-item-icon-min{
        font-size: 0;
        float: left;
        padding-top: 20px;
        padding-left: 10px;
        padding-right: 10px;
    }

    .pay-item-icon-min .icon{
        width: 50px;
        height: 50px;
    }

    .pay-item-title-min{
        font-family: PingFangSC-Regular, tahoma, arial, "Hiragino Sans GB", "Microsoft Yahei", sans-serif;
        padding: 23px 0px 4px 0px;
        text-align: center;
        color: #323334;
        letter-spacing: 0;
        line-height: 16px;
        margin-bottom: .5em;
    }
    .pay-item-tips-min {
        font-family: PingFangSC-Regular, tahoma, arial, "Hiragino Sans GB", "Microsoft Yahei", sans-serif;
        padding: 0px;
        height: 62px;
        text-align: center;
        font-size: 12px;
        color: #757C82;
        letter-spacing: 0;
        line-height: 18px;
    }
    .pay-item-tips-min{
        font-size: 13px;
        padding-left:16px;
        padding-right: 16px;
    }
    .payDetail{
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    div.pre{
        display: block;
        padding: 9.5px;
        margin: 0 0 10px;
        font-size: 13px;
        line-height: 1.42857143;
        color: #333;
        word-break: break-all;
        word-wrap: break-word;
        background-color: #f5f5f5;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    span.money{
        color: red;
        margin: .3em;
    }
    .table td p a{
        margin: 0;
    }
    .table th,.table td{
        white-space: nowrap;
    }
    .gather-info{
        overflow-x: auto;
        max-width:900px;
    }
    .gather-info>div{
        min-width:850px;
    }
</style>
<div class="box gather-info">
    <div class="box-header with-border">

        <ul class="clearfix type-list list-unstyled" style="margin: auto;">
            @foreach($paymentTypes as $type)
                <li class="pay-li-min @if($type->focus) active @endif" data-locate="{{$type->locateLink}}">
                    <div class="pay-item">
                        <div class="pay-item-icon-min">
                            <i class="fa {{$type->icon}}"></i>
                        </div>
                        <h3 class="pay-item-title-min" title="{{$type->name}}-{{$type->code}}">{{$type->name}}</h3>
                        <div class="pay-item-tips-min">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>供应商</h6>
                                    {{ $type->supplier_count }}
                                </div>
                                <div class="col-sm-4">
                                    <h6>总应付</h6>
                                    {{ number_format($type->due_money_sum) }}
                                </div>
                                <div class="col-sm-4">
                                    <h6>已付清</h6>
                                    {{ number_format($type->paid_money_sum) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="box-body period-info">
        <div class="row">
            {!! $html['table'] !!}
            {{--<div class="col-sm-6">--}}
                {{--{!! $html['schedule'] !!}--}}
            {{--</div>--}}
            {{--<div class="divide"></div>--}}
            {{--<div class="col-sm-6">--}}
                {{--{!! $html['detail']!!}--}}
            {{--</div>--}}
        </div>
    </div>

</div>
{{--<div class="box box-default">--}}
    {{--<div class="box-header with-border">--}}
        {{--<h3 class="box-title">详情</h3>--}}

        {{--<div class="box-tools pull-right">--}}
            {{--<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>--}}
            {{--</button>--}}
            {{--<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{----}}
{{--</div>--}}