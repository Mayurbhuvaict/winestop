<?php
namespace Meigee\Barbour\Block\Adminhtml\System\Config\CheckboxSwitch;

class EnableDisable extends \Meigee\Barbour\Block\Adminhtml\System\Config\CheckboxSwitch
{
    function getOnLabel()
    {
        return __('Enable');
    }
    function getOffLabel()
    {
        return __('Disable');
    }
}
