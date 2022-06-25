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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Exception;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;

/**
 * Class CardsMassDelete
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class CardsMassDelete extends Pool
{
    /**
     * Coupons mass delete action
     *
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if (!$pool->getId()) {
            $this->_forward('noroute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {
            /** @var Collection $collection */
            $collection = $this->_objectManager->create(Collection::class)
                ->addFieldToFilter('giftcard_id', ['in' => $codesIds])
                ->addFieldToFilter('pool_id', $pool->getId());

            /** @var GiftCard $giftCard */
            foreach ($collection as $giftCard) {
                $giftCard->delete();
            }
        }
    }
}
