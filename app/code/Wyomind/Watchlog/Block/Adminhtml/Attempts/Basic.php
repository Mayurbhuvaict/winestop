<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Block\Adminhtml\Attempts;

/**
 * Data feed grid container
 */
class Basic extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function __construct(\Wyomind\Watchlog\Helper\Delegate $wyomind, \Magento\Backend\Block\Widget\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->watchlogHelper->checkWarning();
        parent::__construct($context, $data);
    }
    /**
     * Block constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_attempts_basic';
        $this->_blockGroup = 'Wyomind_Watchlog';
        $this->_headerText = __('Watchlog > Login Attempts');
        $this->setTemplate('basic.phtml');
        parent::_construct();
        $this->buttonList->remove('add');
    }
    /**
     * @return string
     */
    public function isPeriodicalReportEnabled()
    {
        return $this->watchlogHelper->getDefaultConfig("watchlog/periodical_report/enable_reporting");
    }
}