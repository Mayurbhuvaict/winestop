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

namespace Mageplaza\GiftCard\Pricing\Render;

use Magento\Wishlist\Model\Item;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Class for wishlist_price rendering
 */
class WishlistPriceBox extends FinalPriceBox
{
    /**
     * @return bool
     */
    public function isFixedPrice()
    {
        return $this->getAmount() === null ? parent::isFixedPrice() : true;
    }

    /**
     * @return FinalPriceBox|$this
     */
    protected function findMinMaxValue()
    {
        $requestAmount = $this->getAmount();

        if ($requestAmount === null) {
            return parent::findMinMaxValue();
        }

        $amountJson = $this->saleableItem->getGiftCardAmounts() ?: [];
        $amounts    = is_string($amountJson) ? Data::jsonDecode($amountJson) : $amountJson;

        foreach ($amounts as $amount) {
            if ((float) $amount['amount'] === $requestAmount) {
                $this->_minMax = [
                    'min' => $amount['price'],
                    'max' => 0
                ];
            }
        }

        return $this;
    }

    /**
     * @return float
     */
    protected function getAmount()
    {
        /** @var Item $item */
        $item = $this->getItem();

        if (!$item) {
            return null;
        }

        $buyRequest = $item->getBuyRequest()->getData();

        if (!isset($buyRequest['amount'])) {
            return null;
        }

        return (float) $buyRequest['amount'];
    }
}
