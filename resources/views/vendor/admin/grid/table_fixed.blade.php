<style>
    .table th,.table td{
        white-space: nowrap;
        border:1px solid #efefef;
        padding:.3em!important;
    }
</style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title" title="fixed">
        </h3>
        <div class="pull-right">
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>

        <span>
            {!! $grid->renderHeaderTools() !!}
        </span>
            {{--{!! $grid->getFilter()->filters() !!}--}}
    </div>
    <!-- /.box-header -->
    <div id="filter_div" class="box-header" style="border-top:#efefef 1px solid; margin-top: 0;">
        {!! $grid->renderFilter() !!}
    </div>
    <div id="scroll-div" class="scroll-offset">
        <div id="scroll-sync-table"></div>
    </div>
    <div id="data-div" class="box-body table-responsive no-padding">
        <table id="data-table" class="table table-hover" style="">

            <tr class="ele-fixed bg-gray">
                @foreach($grid->columns() as $column)
                <th>{{$column->getLabel()}}{!! $column->sorter() !!}</th>
                @endforeach
            </tr>

            <tr class="ele-fixed counter bg-gray" style="margin-top:-3px">{!! substr($grid->renderFooter(), 4, -5) !!}</tr>

            @foreach($grid->rows() as $row)
            <tr {!! $row->getRowAttributes() !!}>
                @foreach($grid->columnNames as $name)
                <td {!! $row->getColumnAttributes($name) !!}>
                    {!! $row->column($name) !!}
                </td>
                @endforeach
            </tr>
            @endforeach



        </table>
    </div>
    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>
