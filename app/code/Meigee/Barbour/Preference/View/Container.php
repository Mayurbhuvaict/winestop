<?php

namespace Meigee\Barbour\Preference\View;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Reader\Context;

class Container extends \Magento\Framework\View\Layout\Reader\Container
{
    private $_viewCheck;

    public function __construct(
        Layout\ScheduledStructure\Helper $helper,
        Layout\ReaderPool $readerPool,
        \Meigee\Barbour\Model\ViewCheck $viewCheck
    )
    {
        $this->_viewCheck = $viewCheck;
        parent::__construct($helper, $readerPool);
    }

    public function interpret(Context $readerContext, Layout\Element $currentElement)
    {
        $checkResult = $this->_viewCheck->check($currentElement);
        if ($checkResult === false)
        {
            return $this;
        }
        return parent::interpret($readerContext, $currentElement);
    }
}
