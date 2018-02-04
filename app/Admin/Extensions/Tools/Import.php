<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Import extends AbstractTool
{

    protected $action;

    protected function script()
    {
    }

    public function setAction($action)
    {

        $this->action = $action;

        return $this;
    }

    public function render()
    {
        // Admin::script($this->script());


        $action = $this->action;

        return view('admin::tools.import', compact('action'));
    }
}