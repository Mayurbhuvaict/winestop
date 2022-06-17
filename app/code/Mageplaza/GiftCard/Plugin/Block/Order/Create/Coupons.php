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

namespace Mageplaza\GiftCard\Plugin\Block\Order\Create;

use Mageplaza\GiftCard\Helper\Checkout as CheckoutHelper;

/**
 * Class Coupons
 * @package Mageplaza\GiftCard\Plugin\Block\Order\Create
 */
class Coupons
{
    /**
     * @var CheckoutHelper
     */
    protected $helper;

    /**
     * Coupons constructor.
     *
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(CheckoutHelper $checkoutHelper)
    {
        $this->helper = $checkoutHelper;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Coupons $subject
     * @param                                                     $coupon
     *
     * @return mixed
     */
    public function afterGetCouponCode(\Magento\Sales\Block\Adminhtml\Order\Create\Coupons $subject, $coupon)
    {
        if (!$this->helper->isEnabled() || !$this->helper->isUsedCouponBox()) {
            return $coupon;
        }

        $giftCards = $this->helper->getGiftCardsUsed();
        if (count($giftCards)) {
            return array_keys($giftCards)[0];
        }

        return $coupon;
    }
}
