<style>
        /*.table th,.table td{*/
            /*white-space: nowrap;*/
            /*border:1px solid #efefef;*/
            /*padding:.3em!important;*/
        /*}*/
</style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title" title="layui">
        </h3>
        <div class="pull-right">
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>

        <span>
            {!! $grid->renderHeaderTools() !!}
        </span>
    </div>
    <!-- /.box-header -->
    <div id="filter_div" class="box-header" style="border-top:#efefef 1px solid; margin-top: 0;">
        {!! $grid->renderFilter() !!}
    </div>
    {{--<div id="scroll-div" class="scroll-offset">--}}
        {{--<div id="scroll-sync-table"></div>--}}
    {{--</div>--}}
    <div id="data-div" class="box-body table-responsive no-padding">
        <table id="data-table" class="table table-hover" style="display: none">
            <thead class="bg-gray">
                @foreach($grid->columns() as $column)
                <th @include("vendor.admin.grid.table.tr-lay-data",  ['type'=>'thead', 'column'=>$column])>{{$column->getLabel()}}{!! $column->sorter() !!}</th>
                @endforeach
            </thead>
            <tbody>
            @foreach($grid->rows() as $row)
            <tr  {!! $row->getRowAttributes() !!}>
                @foreach($grid->columnNames as $name)
                    <td @include("vendor.admin.grid.table.tr-lay-data",  ['type'=>'thbody', 'column'=>($grid->columns()[$loop->index])])  {!! $row->getColumnAttributes($name) !!}>
                    {!! $row->column($name) !!}
                </td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>
