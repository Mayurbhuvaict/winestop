<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GiftCard\Controller\Adminhtml\Code;

/**
 * Class Delete
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Code
 */
class Delete extends Code
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $giftCard = $this->_initObject();
        if ($giftCard && $giftCard->getId()) {
            try {
                $giftCard->delete();
                $this->messageManager->addSuccessMessage(__('The gift card was deleted successfully.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }
}
