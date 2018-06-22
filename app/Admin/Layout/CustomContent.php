<?php

namespace App\Admin\Layout;

use Encore\Admin\Layout\Content;
use Illuminate\Contracts\Support\Renderable;

class CustomContent implements Renderable
{
    /**
     * 其他参数
     * @var
     */
    protected $variables;

    /**
     * @Content $content
     */
    protected $content;

    public function __construct(Content $extContent, $useSinglePage = false)
    {
        $this->setVariable('useSinglePage', $useSinglePage);

        $this->content =  $extContent;
    }

    public function setVariable($name, $value)
    {
        if(null != $name)
        {
            $this->variables[$name] = $value;
        }
        return $this;
    }

    public function getVariable($name)
    {
        if(empty($name))
        {
            return $this->variables;
        }
        elseif(isset($this->variables[$name]))
        {
            return $this->variables[$name];
        }else{
            return null;
        }
    }


    public function render()
    {
        session()->flash('session_flash', $this->variables);

        return $this->content->render();
    }
}
