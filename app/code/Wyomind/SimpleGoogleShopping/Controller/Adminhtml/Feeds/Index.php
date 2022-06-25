<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds;

/**
 * Index action (grid)
 */
class Index extends \Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds
{
    /**
     * Execute action
     */
    public function execute()
    {
        $this->heartbeatHelper->checkHeartbeat();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Google Shopping > Data Feeds'));
        $resultPage->addBreadcrumb(__('Simple Google Shopping'), __('Simple Google Shopping'));
        $resultPage->addBreadcrumb(__('Manage Data Feeds'), __('Manage Data Feeds'));

        return $resultPage;
    }
}
