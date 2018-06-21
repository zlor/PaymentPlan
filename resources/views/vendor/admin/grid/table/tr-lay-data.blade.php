@if($type=="thead")
  lay-data="@if(is_array($lay_data = \Encore\Admin\Grid\Column::getAttributes($column->getName()))){{$lay_data['lay-data']}}@else{field:'{{$column->getName()}}'}@endif"
@else
{{--  lay-data="@if(is_array($lay_data = \Encore\Admin\Grid\Column::getAttributes($column->getName()))){{$lay_data['lay-data']}}@else{field:'{{$column->getName()}}'}@endif"--}}
@endif