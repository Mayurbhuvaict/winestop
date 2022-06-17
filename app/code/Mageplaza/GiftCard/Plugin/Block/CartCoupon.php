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

namespace Mageplaza\GiftCard\Plugin\Block;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Mageplaza\GiftCard\Helper\Checkout as CheckoutHelper;

/**
 * Class CartCoupon
 * @package Mageplaza\GiftCard\Plugin
 */
class CartCoupon
{
    /**
     * @var CheckoutHelper
     */
    protected $helper;

    /**
     * CartCoupon constructor.
     *
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(CheckoutHelper $checkoutHelper)
    {
        $this->helper = $checkoutHelper;
    }

    /**
     * @param Coupon $subject
     * @param                                     $coupon
     *
     * @return mixed
     */
    public function afterGetCouponCode(Coupon $subject, $coupon)
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

    /**
     * @param Coupon $subject
     * @param string $html
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(Coupon $subject, $html)
    {
        $giftCardHtml = $subject->getLayout()
            ->createBlock(
                Template::class,
                'mageplaza.gift.card.checkout.cart.coupon'
            )
            ->setTemplate('Mageplaza_GiftCard::cart/coupon.phtml');

        return $giftCardHtml->toHtml() . $html;
    }
}
