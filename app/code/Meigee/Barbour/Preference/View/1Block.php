<?php

namespace Meigee\Barbour\Preference\View;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Reader\Context;
use Magento\Framework\Data\Argument\InterpreterInterface;


class Block extends \Magento\Framework\View\Layout\Reader\Block
{
    private $_viewCheck;

    public function __construct(
        Layout\ScheduledStructure\Helper $helper,
        Layout\Argument\Parser $argumentParser,
        Layout\ReaderPool $readerPool,
        InterpreterInterface $argumentInterpreter,
        \Meigee\Barbour\Model\ViewCheck $viewCheck,
        $scopeType = null
    )
    {
         $this->_viewCheck = $viewCheck;
         parent::__construct($helper, $argumentParser, $readerPool, $argumentInterpreter, $scopeType);
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
