<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\CronScheduler\Block\Adminhtml;

/**
 * Message to upgrade to the pro version when using the free version
 * @version 1.0.0
 */
class UpgradeToPro extends \Magento\Backend\Block\Template
{
    public function __construct(\Wyomind\CronScheduler\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $data);
        $this->setTemplate('upgradeToPro.phtml');
    }
    /**
     * Using the pro version ?
     * @return type
     */
    public function isPro()
    {
        return $this->_moduleList->has('Wyomind_CronSchedulerPro');
    }
}