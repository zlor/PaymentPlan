<?php

namespace App\Admin\Controllers\Pay\Batch;

use App\Admin\Controllers\Pay\BatchController;
use App\Admin\Extensions\Tools\AreaEdit;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Grid\Tools;
use Illuminate\Http\Request;

/**
 * Class FinalController
 *
 * 二次核定
 *
 * @package App\Admin\Controllers\Pay\Batch
 */
class FinalController extends BatchController
{
    protected $routeMap = [
        'index' => 'payment.schedule.final.batch',
        'store' => 'final.schedule.store.batch',
        'editFinal' => 'final.schedule.update.batch',

    ];

    protected $batch_column = 'final_due_money';

    public function index(Request $request)
    {
        $content = parent::index($request);

        $content->header(trans('final.payment.schedule'));
        $content->description(trans('final.page.batch'));

        $content->breadcrumb(
            ['text'=>'付款管理', 'url'=>'#'],
            ['text'=>'一次核定-批量调整', 'url'=> $this->getUrl('index')]
        );

        return $content;
    }

    protected function _effectBatchGrid()
    {
        $grid = parent::_effectBatchGrid();

        $grid->tools(function(Tools $tools){

            ##
            $tool_open_edit = new AreaEdit('finalArea', 'finalHead');

            $tool_open_edit->setInputType();

            $tools->append($tool_open_edit);

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

            $actions->disableDelete();
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
