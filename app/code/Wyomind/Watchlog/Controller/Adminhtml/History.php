<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Controller\Adminhtml;

/**
 * Class History
 * @package Wyomind\Watchlog\Controller\Adminhtml
 */
abstract class History extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory|null
     */
    public $resultRedirectFactory = null;

    /**
     * @var null|\Wyomind\Watchlog\Cron\History
     */
    public $history = null;

    /**
     * History constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Wyomind\Watchlog\Cron\History $history
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\Watchlog\Cron\History $history
    ) {
    
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->history = $history;
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
