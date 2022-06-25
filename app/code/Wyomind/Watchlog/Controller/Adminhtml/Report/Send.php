<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Controller\Adminhtml\Report;

/**
 * Class Send
 * @package Wyomind\Watchlog\Controller\Adminhtml\Report
 */
class Send extends \Wyomind\Watchlog\Controller\Adminhtml\Report
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $emails = explode(',', $this->watchlogHelper->getDefaultConfig("watchlog/periodical_report/emails"));
        $this->periodicalReport->sendReport($emails);
        
        return $this->resultRedirectFactory->create()->setPath('watchlog/attempts/'.$this->getRequest()->getParam('previous'));
    }
}
