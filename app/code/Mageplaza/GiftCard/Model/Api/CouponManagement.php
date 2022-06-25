<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

namespace Mageplaza\GiftCard\Model\Api;

use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftCard\Helper\Checkout;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;

/**
 * Class CouponManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class CouponManagement implements CouponManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * CouponManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param Checkout $helper
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Checkout $helper,
        GiftCardFactory $giftCardFactory
    ) {
        $this->quoteRepository  = $quoteRepository;
        $this->_helper          = $helper;
        $this->_giftCardFactory = $giftCardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if ($this->isEnableGiftCard()) {
            $giftCards = $this->_helper->getGiftCardsUsed($quote);
            if (count($giftCards)) {
                return array_keys($giftCards)[0];
            }
        }

        return $quote->getCouponCode();
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $isUsedGiftCard = false;
            if ($this->isEnableGiftCard()) {
                /** @var GiftCard $giftCard */
                $giftCard = $this->_giftCardFactory->create();
                $giftCard->loadByCode($couponCode);
                if ($giftCard->isActive($quote)) {
                    $this->_helper->setGiftCards($giftCard->getCode(), $quote);
                    $isUsedGiftCard = true;
                }
            }
            if (!$isUsedGiftCard) {
                $quote->setCouponCode($couponCode);
                $this->quoteRepository->save($quote->collectTotals());
            }
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply coupon code'));
        }
        if (!$isUsedGiftCard && ($quote->getCouponCode() !== $couponCode)) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $isRemovedGiftCard = false;
            if ($this->isEnableGiftCard()) {
                $giftCards = $this->_helper->getGiftCardsUsed($quote);
                if ($giftCards && count($giftCards)) {
                    $this->_helper->removeGiftCard(null, true, $quote);
                    $isRemovedGiftCard = true;
                }
            }
            if (!$isRemovedGiftCard) {
                $quote->setCouponCode('');
            }

            $this->quoteRepository->save($quote->collectTotals());
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        if (!$isRemovedGiftCard && ($quote->getCouponCode() !== '')) {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isEnableGiftCard()
    {
        return $this->_helper->isEnabled() && $this->_helper->isUsedCouponBox();
    }
}
