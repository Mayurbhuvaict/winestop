<?php
namespace Meigee\Barbour\Block\Adminhtml\System\Config\CheckboxSwitchHeader;

class OnOffHeaders extends \Meigee\Barbour\Block\Adminhtml\System\Config\CheckboxSwitchHeader
{
    protected $header = true;

    function getOnLabel()
    {
        return __('On');
    }
    function getOffLabel()
    {
        return __('Off');
    }
}
