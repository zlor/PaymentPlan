<form id="import_config" method="POST" action="" class="form-horizontal" accept-charset="UTF-8" pjax-container="1">
    <div class="box-body fields-group">
        <input type="hidden" name="payment_file_id" value="" class="payment_file_id">
        <div class="form-group  ">
            <label for="name" class="col-sm-3 control-label">文件名</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                    <input disabled="1" type="text" id="name" name="name" value="" class="form-control name" placeholder="输入 文件名">
                </div>
            </div>
        </div>
        <div class="form-group  ">
            <label for="import_mapping" class="col-sm-3 control-label">表头对照</label>
            <div class="col-sm-9">
                <input type="hidden" name="import_mapping">
                <select class="form-control import_mapping" style="width: 100%;" name="import_mapping">
                    <option value=""></option>
                    @foreach($import_mapping_options as $key=> $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group  ">
            <label for="skip_row_number" class="col-sm-3 control-label">开始行数</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <div class="input-group">
                        <input style="width: 150px; text-align: center;" type="text" id="skip_row_number" name="skip_row_number" value="" class="form-control skip_row_number initialized" placeholder="可不填">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group  ">
            <label for="skip_column_number" class="col-sm-3 control-label">开始列数</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <div class="input-group">
                        <input style="width: 150px; text-align: center;" type="text" id="skip_column_number" name="skip_column_number" value="" class="form-control skip_column_number initialized" placeholder="可不填">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        {{ csrf_field() }}
        {{ method_field('POST') }}
        <input type="hidden" id="hidden_url" name="url">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            <div class="btn-group pull-left">
                <button type="reset" class="btn btn-warning pull-right">撤销</button>
            </div>
            <div class="btn-group pull-right">
                <button type="button" id="importBtn" class="btn btn-info pull-right">载入</button>
            </div>

        </div>

    </div>
</form>