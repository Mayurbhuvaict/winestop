<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Controller\Adminhtml\Attempts;

/**
 * Index action (grid)
 */
class Advanced extends \Wyomind\Watchlog\Controller\Adminhtml\Attempts
{

    /**
     * Execute action
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu("Magento_Backend::stores");
        $resultPage->getConfig()->getTitle()->prepend(__('Watchlog > Login Attempts'));
        $resultPage->addBreadcrumb(__('Watchlog'), __('Watchlog'));
        return $resultPage;
    }
}
