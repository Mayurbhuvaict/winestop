<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Controller\Adminhtml\History;

/**
 * Class Purge
 * @package Wyomind\Watchlog\Controller\Adminhtml\History
 */
class Purge extends \Wyomind\Watchlog\Controller\Adminhtml\History
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $this->history->purge();
        return $this->resultRedirectFactory->create()->setPath('watchlog/attempts/' . $this->getRequest()->getParam('previous'));
    }
}
