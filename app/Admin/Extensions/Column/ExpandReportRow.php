<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class ExpandReportRow extends AbstractDisplayer
{

    private $useAjax = false;

//    public function display(\Closure $callback = null, $btn = '')
//    {
//        $callback = $callback->bindTo($this->row);
//
//        $html = call_user_func($callback);
//
//        if($this->useAjax)
//        {
//            $script = <<<EOT
//$('.grid-expand').on('click', function () {
//    if ($(this).data('inserted') == '0') {
//        var key = $(this).data('key');
//        var row = $(this).closest('tr');
//        var html = $('template.grid-expand-'+key).html();
//
//        row.after("<tr><td colspan='"+row.find('td').length+"' style='padding:0 !important; border:0px;'>"+html+"</td></tr>");
//
//        $(this).data('inserted', 1);
//    }
//
//    $("i", this).toggleClass("fa-caret-right fa-caret-down");
//});
//EOT;
//        }else{
//            $script = <<<EOT
//$('.grid-expand').on('click', function () {
//    if ($(this).data('inserted') == '0') {
//        var key = $(this).data('key');
//        var row = $(this).closest('tr');
//        var html = $('template.grid-expand-'+key).html();
//
//        $.get(url, params, function(html){
//
//        });
//
//        row.after("<tr><td colspan='"+row.find('td').length+"' style='padding:0 !important; border:0px;'>"+html+"</td></tr>");
//
//        $(this).data('inserted', 1);
//    }
//
//    $("i", this).toggleClass("fa-caret-right fa-caret-down");
//});
//EOT;
//
//        }
//
//
//        Admin::script($script);
//
//        $btn = $btn ?: $this->column->getName();
//
//
//        $key = $this->getKey();
//
//        return <<<EOT
//<a class="btn btn-xs btn-default grid-expand" data-inserted="0" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
//    <i class="fa fa-caret-right"></i> $btn
//</a>
//<template class="grid-expand-{$key}">
//    <div id="grid-collapse-{$key}" class="collapse">$html</div>
//</template>
//EOT;
//    }

    public function display(\Closure $callback = null, $options=['btn'=>'', 'data'=>[]])
    {
        $callback = $callback->bindTo($this->row);

        $btn = $options['btn'];

        $data = $options['data'];

        list($latitude, $longitude) = call_user_func($callback);

        $key = $data['key']?$data['key']:$this->getKey();

        $data['key'] = $key;

        $name = $this->column->getName();

        $htmlData = '';
        foreach ($data as $key=>$item)
        {
            $htmlData .= " data-{$key}='{$item}'";
        }

        Admin::script($this->script());

        return <<<EOT
<button class="btn btn-xs btn-default grid-open-chart" {$htmlData}  data-toggle="modal" data-target="#grid-modal-{$name}-{$key}">
    <i class="fa fa-bar-chart"></i> $btn
</button>

<div class="modal" id="grid-modal-{$name}-{$key}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">$btn</h4>
      </div>
      <div class="modal-body">
        <div id="grid-map-$key" style="height:450px;"></div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
EOT;
    }

    protected function script()
    {
        return <<<EOT

$('.grid-open-chart').on('click', function() {

    var key = $(this).data('key');
    var lat = $(this).data('lat');
    var lng = $(this).data('lng');

    var center = new qq.maps.LatLng(lat, lng);

    var container = document.getElementById("grid-map-"+key);
    var map = new qq.maps.Map(container, {
        center: center,
        zoom: 13
    });

    var marker = new qq.maps.Marker({
        position: center,
        draggable: true,
        map: map
    });
});

EOT;
    }
}