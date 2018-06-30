<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;
use App\Admin\Extensions\Tools\AreaEdit;
use App\Admin\Extensions\Tools\BatchPost;
use App\Admin\Extensions\Tools\Import;
use App\Admin\Extensions\Tools\NewWindowLink;
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

        'editPlan' => 'plan.schedule.update.batch',
        'createPlan' =>'base.bill.payment_schedule.create',
    ];

    protected $batch_column = 'plan_due_money';

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

//        $grid->option('allowCreate', true);

        $grid->tools(function(Tools $tools){

            ## 导入链接
            $tool_import = new Import();
            $tool_import->setAction($this->getUrl('excel'));
            $tools->append($tool_import);

            ##
            $tool_open_edit = new AreaEdit('planArea', 'planHead');

            $tool_open_edit->setInputType();

            $tools->append($tool_open_edit);

            $tool_open_create = new NewWindowLink();
            $tool_open_create->setText(trans('admin.new'));
            $tool_open_create->setAction($this->getUrl('createPlan'));
            $tools->append($tool_open_create);

            // ## 批量操作调整
            $tools->disableBatchActions();
            // $tools->batch(function(Tools\BatchActions $actions){
            //     $actions->disableDelete();
            //     // 增加批量修改计划金额操作
            //     $actions->add('保存-计划金额调整', new BatchPost());
            // });

        });

        //
        $grid->actions(function(Actions $actions){

            // 当已存在其他核定信息时，不可编辑
            $paymentSchedule = $this->row;

            // 快速调整界面取消，编辑操作
            $actions->disableEdit();

            // 有权限编辑时，可以允许删除
            if( ! $paymentSchedule->allowPlanEdit())
            {
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
SCRIPT;
        Admin::script($script);
    }
}
