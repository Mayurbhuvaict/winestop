<?php

namespace Meigee\Barbour\Preference\View;

use Magento\Framework\View\Layout\ScheduledStructure\Helper;
use Magento\Framework\View\Layout\Reader\Visibility\Condition;
use Magento\Framework\View\Layout\Reader\Context;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\Config\DataInterfaceFactory;

class UiComponent extends \Magento\Framework\View\Layout\Reader\UiComponent
{
    private $_viewCheck;

    public function __construct(
		Helper $helper,
		\Meigee\Barbour\Model\ViewCheck $viewCheck,
		Condition $conditionReader,
		DataInterfaceFactory $uiConfigFactory,
		ReaderPool $readerPool
	){
        $this->_viewCheck = $viewCheck;
        parent::__construct($helper, $conditionReader, $uiConfigFactory, $readerPool);
    }

    public function interpret(Context $readerContext, Element $currentElement)
    {
        $checkResult = $this->_viewCheck->check($currentElement);
        if ($checkResult === false)
        {
            return $this;
        }
        return parent::interpret($readerContext, $currentElement);
    }
}