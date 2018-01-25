<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use Illuminate\Support\Str;

class Popover extends AbstractDisplayer
{

    protected $needLimit = false;



    public function display($placement = 'left', $options = [])
    {
        $value = $this->value;

        $title = isset($options['title'])?$options['title']:'';

        $limit = isset($options['limit']);

        if($limit)
        {
            $limit_number =  intval($options['limit']);

            $limit_number>0 && $value = Str::limit($value, $limit_number);
        }

        $callEvent = isset($options['trigger']);

        $trigger = 'click';
        if($callEvent)
        {
            $trigger = is_array($options['trigger']) ? join(' ', $options['trigger'])
                                                     : $options['trigger'];
        }

        $callDelay = isset($options['delay']);
        $delay = '';
        if($callDelay)
        {
            $delay = is_array($options['delay']) ? json_encode($options['delay'])
                                                 : $options['delay'];
        }

        if(isset($options['selector']))
        {
            $selector = str_replace(".","_","popover_".$this->column->getName().'_'.$this->getKey());
        }else{
            $selector = false;
        }



        Admin::script("$('[data-toggle=\"popover\"]').popover()");
        return <<<EOT
<button type="button"
    id="{$selector}"
    class="btn btn-secondary"
    title="{$title}"
    data-container="body"
    data-toggle="popover"
    data-trigger="{$trigger}"
    data-delay="{$delay}"
    data-placement="$placement"
    data-content="{$this->value}"
    data-selector="{$selector}"
    >
  {$value}
</button>

EOT;

    }
}