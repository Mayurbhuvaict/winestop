<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchBrowser\Controller\Adminhtml\Browse;

/**
 * Class Index
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_ElasticsearchBrowser::browse';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|null
     */
    public $messageManager = null;

    /**
     * @var null|\Wyomind\ElasticsearchBrowser\Helper\Data
     */
    public $dataHelper = null;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Wyomind\ElasticsearchBrowser\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wyomind\ElasticsearchBrowser\Helper\Data $dataHelper
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $message = $this->dataHelper->getNotificationMessage();
        if ($message != "") {
            $this->messageManager->addNotice($this->dataHelper->getNotificationMessage());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Backend::system');
        $resultPage->getConfig()->getTitle()->prepend(__('Wyomind > Elasticsearch Browser > Browse Data'));

        return $resultPage;
    }
}
