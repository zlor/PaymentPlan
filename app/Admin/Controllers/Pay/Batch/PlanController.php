<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;
use App\Admin\Extensions\Tools\BatchPost;
use App\Admin\Extensions\Tools\Import;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Grid\Tools;
use Illuminate\Http\Request;

/**
 * Class PlanController
 *
 * 计划应付款
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class PlanController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.plan.batch',
        'store' => 'plan.schedule.store.batch',

        'excel'  => 'payment.plan.excel',
    ];

    public function index(Request $request)
    {
        $content = parent::index($request);

        $content->header(trans('plan.payment.schedule'));
        $content->description(trans('plan.page.batch'));

        $content->breadcrumb(
            ['text'=>'付款管理', 'url'=>'#'],
            ['text'=>'录入计划-批量调整', 'url'=> $this->getUrl('index')]
        );

        return $content;
    }

    protected function _effectBatchGrid()
    {
        $grid = parent::_effectBatchGrid();

        $grid->tools(function(Tools $tools){

            ## 导入链接
            $tool_import = new Import();

            $tool_import->setAction($this->getUrl('excel'));

            $tools->append($tool_import);

            ## 批量操作调整
            $tools->batch(function(Tools\BatchActions $actions){

                $actions->disableDelete();

                // 增加批量修改计划金额操作
                $actions->add('保存-计划金额调整', new BatchPost());

            });

        });

        //
        $grid->actions(function(Actions $actions){

            // 当已存在其他核定信息时，不可编辑
            $paymentSchedule = $this->row;

            if( ! $paymentSchedule->allowPlanEdit())
            {
                $actions->disableEdit();
                $actions->disableDelete();
            }
        });

        $this->_adaptMultiCellEdit();

        return $grid;
    }

    /**
     * 适配多行编辑更新
     */
    protected function _adaptMultiCellEdit()
    {
        $script = <<<SCRIPT
    $('.planArea ul li.show-info').click(function(){
        var ul = $(this).parent('ul');
        ul.find('li.show-info').hide();
        ul.find('li.edit-info').removeClass('hide');
        ul.find('li.edit-message-info').removeClass('hide');
        ul.find('li.edit-info').html('<div class="input-group"><span class="input-group-btn"><button type="button" class="cacheEdit btn btn-sm btn-default"><i class="fa fa-check" title="暂存"></i></button></span><input type="text" name="editPlanMoney" class="form-control input-sm"></div>');
    });
    
    $('.planArea ul ').on('click', 'button.cacheEdit', function(){
        $(this).parents('li.edit-info').addClass('hide');
        $(this).parents('ul').find('li.edit-message-info').addClass('hide');
        $(this).parents('ul').find('li.show-info').show();
    });
    $('.planArea ul ').on('change', 'input.editPlanMoney', function(){
        var message = 'test';
        $(this).parents('ul').find('li.edit-message-info').html(message);
    });
    
SCRIPT;
        Admin::script($script);
    }
}
