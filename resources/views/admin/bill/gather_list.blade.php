<style>
    .ext-icon {
        width: 1em;
    }
    .period-list{
        min-height: 480px;
    }
    .period-list li:hover{
        cursor: pointer;
        /*border: 1px red solid;*/
        background-color:#efefef;
    }
    span.litter-font .fa-text{
        font-size: .5em;
    }
    .installed {
        color: #00a65a;
        margin-right: 10px;
    }
    .product-img{
        width: 6.8em;
    }
    #bill_period_list li.active{
        background: #efefef;
    }
</style>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">账期</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body period-list">
        <ul id="bill_period_list" class="products-list product-list-in-box">
            @foreach($items as $item)
                <li class="item @if($item->focus) active @endif" data-locate="{{$item->locateLink}}">
                    <div class="product-img">
                        @if(in_array($item['status'], ['close']))
                            <span class="litter-font"  title="{{ __('bill.period.status.close') }}">
                                <i class="fa fa-ban ext-icon text-gray"></i>
                                <i class="fa fa-text text-gray">{{ $item['month'] }}</i>
                            </span>
                        @elseif(in_array($item['status'], ['lock']))
                            <span class="litter-font" title="{{ __('bill.period.status.lock') }}">
                                <i class="fa fa-lock ext-icon text-blue"></i>
                                <i class="fa fa-text text-gray">{{ $item['month'] }}</i>
                            </span>
                        @elseif(in_array($item['status'], ['standby']))
                            <span class="litter-font"  title="{{ __('bill.period.status.standby') }}">
                                <i class="fa fa-check ext-icon text-gray"></i>
                                <i class="fa fa-text text-gray">{{ $item['month'] }}</i>
                            </span>
                        @else
                            <span class="litter-font"  title="{{ __('bill.period.status.active') }}">
                                <i class="fa fa-check ext-icon text-green"></i>
                                <i class="fa fa-text text-green">{{ $item['month'] }}</i>
                            </span>
                        @endif
                    </div>
                    <div class="product-info">
                        <span href="#" class="product-title">
                            {{ $item['name'] }}
                        </span>
                        <span class="pull-right installed">
                            @if(in_array($item['status'], ['standby']))
                            <a href="{{ $item['edit'] }}" target="_blank" class="product-title" title="设置就绪中的账期">
                                <i class="fa fa-gear"></i>
                            </a>
                            @endif
                        </span>
                    </div>
                </li>
        @endforeach

        <!-- /.item -->
        </ul>
    </div>
    <!-- /.box-body -->
    <div class="box-footer text-center">
        <a href="{{ $page['url']['baseBillPeriod'] }}" target="_blank" class="uppercase">查看所有档案</a>
    </div>
    <!-- /.box-footer -->
</div>

<script>
    $(function(){
        $('.period-list li').on('click', function(){
            var url = $(this).data('locate');
            $.pjax({
                url: url,
                container: '#pjax-container'
            });
        })
    });
</script>