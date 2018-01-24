<?php

namespace App\Admin\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Models\BillPeriod;
use App\Models\PaymentFile;
use App\Models\PaymentSchedule;
use App\Models\PaymentType;
use App\Models\UserEnv;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;


class ExcelController extends Controller
{

    protected $routeMap = [
        'index'  => 'payment.plan.excel',
        'paymentIndex' => 'payment.plan.index',

        'upload' => 'payment.plan.file.upload',
        'remove' => 'payment.plan.file.remove',
        'import' => 'payment.plan.file.import',
        'download' => 'payment.plan.file.download',
        'info'     => 'payment.plan.file.info',

    ];

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('付款计划(Excel导入)' );

            $content->breadcrumb(
                ['text'=>'付款管理', 'url'=>'#'],
                ['text'=>'计划录入', 'url'=> $this->getUrl('paymentIndex')],
                ['text'=>'Excel导入']
            );

            $inputs = Input::get();

            /**
             * 选择账期
             */
            // 若选择了账期
            if(isset($inputs['default_bill_period_id']))
            {
                $defaultPeriod = BillPeriod::query()->find($inputs['default_bill_period_id']);
            }
            // 否则使用用户的环境配置账期
            if(empty($defaultPeriod))
            {
                $defaultPeriod  = UserEnv::getCurrentPeriod();
            }

            $periods = BillPeriod::query()->whereIn('status', [BillPeriod::STATUS_ACTIVE, BillPeriod::STATUS_STANDYBY])->get();

            $options = [];

            foreach ($periods as $period)
            {
                $options[] = [
                    'text'      =>  $period->name . '_' . $period->month . '('. trans('bill.period.status.'.$period->status) .')',
                    'value'     =>  $period->id,
                    'selected'  =>  $defaultPeriod->id == $period->id,
                ];
            }

            $selectBillPeriod = view('admin.bill.select_periods', compact('options'));;

            // 副标题, 设置默认账期
            $content->description($selectBillPeriod);


            $filter = [
                'bill_period_id'=>$defaultPeriod->id
            ];

            $content->row($this->schedule_header());


            $content->row(function(Row $row)use($filter){

                    $row->column(8, function (Column $column)use($filter) {

                        $tabPanel = new Tab();

                        $tabPanel->add('已上传的文件',$this->schedule_file_list($filter), true);

                        $tabPanel->add('上传文件',    $this->schedule_file_form($filter), false);

                        $column->append($tabPanel);
                    });

                    $row->column(4, function (Column $column)use($filter) {

                        $column->append(new Box('载入预设', $this->schedule_file_import($filter)));

                        $column->append(new Box('相关信息', '<pre id="aboutFile"></pre>'));
                    });

            });
        });
    }

    /**
     * 付款计划头部
     *
     * @param $filter
     *
     * @return string
     */
    protected function schedule_header($filter = [])
    {

        $page = [];

        return view('admin.bill.excel', compact('page'));
    }


    /**
     * 付款计划-数据源文件列表
     *
     * @param $filter array
     *
     * @return Table
     */
    protected function schedule_file_list($filter = [])
    {

        $query = PaymentFile::query();

        if($filter['bill_period_id'])
        {
            $query->where('bill_period_id', $filter['bill_period_id']);
        }

        $files = $query->get();

        $list = [];
        foreach ($files as $file)
        {
            $span = '<span class="showMsg" data-url="'.($this->getUrl('info', ['id'=>$file->id])).'"></span>';
            $actionImport = '<a class="btn btn-default file-import" data-id="'.($file->id).'" data-name="'.($file->name).'" data-path="'.($file->path).'" data-url="'.($this->getUrl('import', ['id'=>$file->id])).'" title="选中文件"><i class="fa fa-gear"></i>选择</a>';
            $actionDownload ='<a href="'.($this->getUrl('download', ['id'=>$file->id])).'" target="_blank" class="btn btn-default" title="下载"><i class="fa fa-download"></i>下载</a>';
            $actionRemove = '<a class="btn btn-default file-delete" data-path="'.($file->path).'"  data-url="'.($this->getUrl('remove', ['id'=>$file->id])).'"><i class="fa fa-trash"></i>删除</a>';

            $actions = '<div class="btn-group btn-group-xs">'
                     . ($file->importSuccess()?'':$actionImport)
                     . $actionDownload
                     . ($file->importSuccess()?'':$actionRemove)
                     . '</div>'
                     . $span;

            // 设置操作行
            $list[] = [
                'name' => $file->name,
                'bill_period.name' => $file->bill_period_name,
                'type' => $file->payment_type_name,
                'size' => $file->sizeTxt,
                'status' => $file->statusExt,
                'action' =>$actions
            ];
        }

        $table = new Table();

        $table->setHeaders([
            'name'=>'文件名', 'bill_period.name'=>'账期', 'type'=>'类型', 'size'=>'大小', 'status'=>'状态', '操作',
        ]);

        $table->setRows($list);

        $script = <<<SCRIPT
$(function () {
    $('.file-delete').click(function () {
        var url  = $(this).data('url'),
            path = $(this).data('path');
        swal({
            title: "确认删除?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            closeOnConfirm: false,
            cancelButtonText: "取消"
        },
        function(){
            $.ajax({
                method: 'delete',
                url: url,
                data: {
                    'files[]':[path],
                    _token:LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.status) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                    }
                }
            });
        });
    });
});
SCRIPT;

        Admin::script($script);


        return $table;
    }

    /**
     * 付款计划-数据源文件表单
     *
     * @param $filter
     *
     * @return Form
     */
    protected function schedule_file_form($filter)
    {
        $form = new Form();

        // 设置上传路径
        $form->action($this->getUrl('upload'));

        $bill_period_id = $form->select('bill_period_id', '账期')
            ->options(PaymentSchedule::getBillPeriodOptions());

        if($filter['bill_period_id'])
        {
            $bill_period_id->default($filter['bill_period_id']);
        }


        $form->select('payment_type_id', '物料类型')
            ->options(PaymentSchedule::getPaymentTypeOptions());

        $form->file('file', '上传文件');

        $form->text('name', '文件重命名')->rules('nullable');

        $form->textarea('memo', '备注');

        return $form;
    }


    /**
     * 付款计划文件
     * @param $filter
     */
    protected function schedule_file_import($filter)
    {
        $data = [];

        $import_mapping_options = PaymentSchedule::getImportMappingOptions();

        $scirpt =<<<SCIPRT
$(function(){
    function activeTr(tr){
        $('.table').find('tr').removeClass('active');
        $('.table').find(tr).addClass('active');
    }
    
    function showMsgTr(tr){
        $('.table').find('tr').removeClass('focus');
        $('.table').find(tr).addClass('focus');
    }
    
    $('tbody tr').click(function(){
        var url = $(this).find('span.showMsg').data('url'),
            params = {};
            
        params._token = LA.token;
        
        showMsgTr($(this));
        
        $.get(url, params, function(data){
        
            var html = '', msgList = data.data.import_msg;
            for(var i = 0; i<msgList.length; i++)
            {
                html += msgList[i]+'<br>';
            }
            $('#aboutFile').html(html);
        }, 'json');
    });
    
    
    $('.file-import').click(function () {
        var url  = $(this).data('url'),
            path = $(this).data('path'),
            form = $('#import_config');
            
        activeTr($(this).parents('tr'));
        
        $('[name="payment_file_id"]', form).val($(this).data('id'));
        
        $('[name="name"]', form).val($(this).data('name'));
        
        $('[name="url"]', form).val(url);
        
    });
    
    $('#importBtn').click(function(){
        var form = $('#import_config'), 
            url = $('[name="url"]', form).val(),
            param = {};
        param = form.serializeArray();
        // console.log(param);
        // param.payment_file_id    = $('[name="payment_file_id"]', form).val();
        // param.name               = $('[name="name"]', form).val();
        // param.import_mapping     = $('[name="import_mapping"]', form).val();
        // param.skip_row_number    = $('[name="skip_row_number"]', form).val();
        // param.skip_column_number = $('[name="skip_column_number"]', form).val();
        
        $.post(url, param, function(data){
            $('#aboutFile').html(data.msg);
            $.pjax.reload('#pjax-container');
        }, 'json');
    });
});
SCIPRT;

        Admin::script($scirpt);


        return view('admin.bill.excel_import', compact('data', 'import_mapping_options'));
    }


    /**
     * 获取文件相关信息
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info($id)
    {
        $file = PaymentFile::query()->find($id);

        if(empty($file))
        {
            return response()->json(['status'=>false, 'message'=>'文件资源未找到~']);
        }

        // 获取导入信息
        return response()->json(['status'=>true, 'message'=>'', 'data'=>$file->toArray()]);
    }



    /**
     * 将指定的文件，导入到数据库中
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function import($id)
    {
        $file = PaymentFile::query()->find($id);

        if(empty($file)){
            return response()->json(['status'=>false, 'message'=>'文件资源未记录，请重新上传！']);
        }

        $inputs = Input::get();
        // 导入使用的参数
        $options = [];

        // 0. 取得默认的方案参数
        if(empty($import_mapping_params = PaymentSchedule::getImportMappingParams($inputs['import_mapping'])))
        {
            return response()->json(['status'=>false, 'message'=>'读取Excel的配置未预设，请联系管理员添加！']);
        }else{
            $options = $import_mapping_params;
        }

        // 取得跳过的行数
        if(!empty($inputs['skip_row_number']))
        {
            $options['skip_row_number'] = $inputs['skip_row_number'];
        }
        // 取得跳过的列数
        if(!empty($inputs['skip_column_number']))
        {
            $options['skip_row_number'] = $inputs['skip_row_number'];
        }

        // 缓存文件
        $result = $file->cacheFile($options);

        // 载入数据
        $result && $result = $file->setupSchedule();

        // 获取载入后的信息
        $data = [
            'status'  => true,
            'message' => $result['msg'],
            'data'    => $result
        ];

        return response()->json(compact('data'));
    }

    /**
     * 将指定的文件，删除
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($id)
    {
        $file = PaymentFile::query()->find($id);

        if(!empty($file))
        {
            // 删除文件(包括原文件)
            $file->remove(true);

            $data= [
                'status'  => true,
                'message' => '已删除',
            ];
        }else{

            $data= [
                'status'  => false,
                'message' => '未找到该文件!',
            ];
        }

        return response()->json($data);
    }


    /**
     * 下载指定的文件
     *
     * @param $id
     */
    public function download($id)
    {
        $file = PaymentFile::query()->find($id);

        if(!empty($file))
        {
            $pathToFile = $file->getLocalPath();
        }else{
            $pathToFile = '';
        }

        try{

            return response()->download($pathToFile);

        }catch (\Exception $e)
        {
            return abort(410);
        }
    }


    /**
     * 上传文件
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request)
    {
        // 验证上传信息
        $this->validate($request, [
            'name' => 'nullable|max:50',
            'file' => 'required|mimes:xlsx,xls',
            'bill_period_id'=>'required',
            'payment_type_id'=>'required',
        ], [],
            [
                'name'              => '重命名',
                'file'              => '文件',
                'bill_period_id'    => '账期',
                'payment_type_id'   => '物料类型'
            ]);

        // 识别账期
        $billPeriod = BillPeriod::query()->findOrNew($request->bill_period_id);

        if(empty($billPeriod->id))
        {
            session()->flash('exception', new MessageBag(['title'=>'异常', 'message'=>'账期未选择！']));

            return redirect()->back();
        }

        // 识别类型
        $paymentType = PaymentType::query()->findOrNew($request->payment_type_id);
        if(empty($paymentType->id))
        {
            session()->flash('exception', new MessageBag(['title'=>'异常', 'message'=>'物料类型未选择！']));

            return redirect()->back();
        }

        // 构造 PaymentFile (已将临时文件转移到)
        $paymentFile = PaymentFile::makeFile($billPeriod, $request->name, $request->file);

        // 加入备注信息
        $paymentFile->memo = $request->memo;

        // 关联账期
        $paymentFile->bill_period()->associate($billPeriod);

        // 关联物料类型
        $paymentFile->payment_type()->associate($paymentType);

        // 保存文件
        $paymentFile->save();

        session()->flash('success', new MessageBag(['title'=>'上传成功！', 'message'=>'']));
        //return redirect()->route($this->routeMap['index']);
        return response()->redirectTo($this->getUrl('index'));
    }

}
