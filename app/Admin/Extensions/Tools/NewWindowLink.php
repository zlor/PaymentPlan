<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class NewWindowLink extends AbstractTool
{

    protected $action;

    protected $text;

    protected function script()
    {
    }

    public function setAction($action)
    {

        $this->action = $action;

        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function render()
    {
        // Admin::script($this->script());

        $action = $this->action;
        $text = $this->text;

        return view('admin::tools.new_window_link', compact('action', 'text'));
    }
}