<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Controller\Adminhtml;

/**
 * Class Report
 * @package Wyomind\Watchlog\Controller\Adminhtml
 */
abstract class Report extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory|null
     */
    public $resultRedirectFactory = null;

    /**
     * @var null|\Wyomind\Watchlog\Cron\PeriodicalReport
     */
    public $periodicalReport = null;

    /**
     * @var null|\Wyomind\Watchlog\Helper\Data
     */
    public $watchlogHelper = null;

    /**
     * Report constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Wyomind\Watchlog\Cron\PeriodicalReport $periodicalReport
     * @param \Wyomind\Watchlog\Helper\Data $watchlogHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\Watchlog\Cron\PeriodicalReport $periodicalReport,
        \Wyomind\Watchlog\Helper\Data $watchlogHelper
    ) {
    
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->periodicalReport = $periodicalReport;
        $this->watchlogHelper = $watchlogHelper;
        parent::__construct($context);
    }

    /**
     * Does the menu is allowed
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_Watchlog::attempts');
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    abstract public function execute();
}
