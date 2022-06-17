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

namespace Mageplaza\GiftCard\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

/**
 * Class Discount
 * @package Mageplaza\GiftCard\Block\Sales\Order
 */
class Discount extends Template
{
    /**
     * Add gift card discount total
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if (abs($source->getGiftCardAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_card',
                    'value'      => $source->getGiftCardAmount(),
                    'base_value' => $source->getBaseGiftCardAmount(),
                    'label'      => __('Gift Cards')
                ]
            ), 'tax');
        }

        if (abs($source->getGiftCreditAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_credit',
                    'value'      => $source->getGiftCreditAmount(),
                    'base_value' => $source->getBaseGiftCreditAmount(),
                    'label'      => __('Gift Credit')
                ]
            ), 'tax');
        }

        return $this;
    }
}
