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

namespace Mageplaza\GiftCard\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;

/**
 * Class Checkout
 * @package Mageplaza\GiftCard\Helper
 */
class Checkout extends Data
{
    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * Checkout constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        CartRepositoryInterface $quoteRepository,
        GiftCardFactory $giftCardFactory
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_giftCardFactory = $giftCardFactory;

        parent::__construct($context, $objectManager, $storeManager, $localeDate);
    }

    /**
     * Collect and save total
     *
     * @param null $quote
     *
     * @return $this
     */
    protected function collectTotals($quote = null)
    {
        if ($this->isAdmin()) {
            return $this;
        }

        if ($quote === null) {
            /** @var Quote $quote */
            try {
                $quote = $this->getCheckoutSession()->getQuote();
            } catch (NoSuchEntityException $e) {
                return $this;
            } catch (LocalizedException $e) {
                return $this;
            }
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);

        $this->_quoteRepository->save($quote->collectTotals());

        return $this;
    }

    /******************************************* Gift Card **********************************************/

    /**
     * Get gift card used
     *
     * @param null $quote
     *
     * @return array|mixed
     */
    public function getGiftCardsUsed($quote = null)
    {
        if ($quote === null) {
            try {
                $quote = $this->getCheckoutSession()->getQuote();
            } catch (NoSuchEntityException $e) {
                return [];
            } catch (LocalizedException $e) {
                return [];
            }
        }

        return self::jsonDecode($quote->getMpGiftCards());
    }

    /**
     * Check quote can used gift card or not
     *
     * @param Quote $quote
     *
     * @return bool
     */
    public function canUsedGiftCard($quote = null)
    {
        if ($quote === null) {
            try {
                $quote = $this->getCheckoutSession()->getQuote();
            } catch (NoSuchEntityException $e) {
                return false;
            } catch (LocalizedException $e) {
                return false;
            }
        }

        if (!$this->isEnabled($quote->getStoreId())) {
            return false;
        }

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() !== GiftCard::TYPE_GIFTCARD) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param      $codes
     * @param null $quote
     *
     * @return $this
     */
    public function setGiftCards($codes, $quote = null)
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        $giftCards = [];
        foreach ($codes as $code) {
            $giftCards[$code] = 0;
        }

        $quote->setMpGiftCards(self::jsonEncode($giftCards));
        $this->collectTotals($quote);

        return $this;
    }

    /**
     * @param $codes
     * @param null $quote
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function addGiftCards($codes, $quote = null)
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        if ($quote === null) {
            $quote = $this->getCheckoutSession()->getQuote();
        }

        foreach ($codes as $code) {
            /** @var \Mageplaza\GiftCard\Model\GiftCard $giftCard */
            $giftCard = $this->_giftCardFactory->create();
            $giftCard->loadByCode($code);
            if (!$giftCard->isActive($quote)) {
                throw new LocalizedException(__('The gift card code "%1" is not valid.', $code));
            }
        }

        $store = $quote->getStore();
        if ($this->isUsedMultipleCode($store)) {
            $giftCardsUsed = array_keys($this->getGiftCardsUsed());
            $codes         = array_unique(array_merge($giftCardsUsed, $codes));
        } elseif (count($codes) > 1) {
            $codes = [array_shift($codes)];
        }

        $this->setGiftCards($codes, $quote);

        return $this;
    }

    /**
     * Remove gift card code from session
     *
     * @param      $code
     * @param bool $removeAll
     * @param null $quote
     *
     * @return $this
     */
    public function removeGiftCard($code, $removeAll = false, $quote = null)
    {
        if ($removeAll) {
            $this->setGiftCards([], $quote);

            return $this;
        }

        $giftCards = $this->getGiftCardsUsed($quote);
        if (array_key_exists($code, $giftCards)) {
            unset($giftCards[$code]);
            $this->setGiftCards(array_keys($giftCards), $quote);
        }

        return $this;
    }

    /******************************************* Gift Credit **********************************************/

    /**
     * Get gift card used
     *
     * @param Quote|null $quote
     *
     * @return float
     */
    public function getGiftCreditUsed($quote)
    {
        return (float) $quote->getGcCredit();
    }

    /**
     * Apply Credit
     *
     * @param float $amount
     * @param Quote $quote
     *
     * @return $this
     * @throws LocalizedException
     */
    public function applyCredit($amount, $quote)
    {
        $balance = $this->getCustomerBalance($quote->getCustomerId());
        if ($amount < 0 || $amount > $balance) {
            throw new LocalizedException(__('Invalid credit amount.'));
        }

        $quote->setGcCredit($amount);
        $this->collectTotals($quote);

        return $this;
    }

    /********************************************** Calculation *****************************************/

    /**
     * Calculate total amount for discount
     *
     * @param Quote $quote
     * @param bool $isCredit
     *
     * @return float|mixed
     */
    public function getTotalAmountForDiscount(Quote $quote, $isCredit = false)
    {
        $discountTotal = $quote->getBaseGrandTotal();
        if (!$quote->isVirtual() && !$this->canUsedForShipping($quote->getStoreId())) {
            $discountTotal -= $quote->getShippingAddress()->getBaseShippingAmount();
        }

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            // todo use configuration to select which type of product can be spent by gift card
            if ($item->getProductType() === GiftCard::TYPE_GIFTCARD) {
                $discountTotal -= $item->getBaseRowTotal();
            }
        }

        if ($isCredit) {
            $discountTotal += $this->getGiftCreditUsed($quote);
        }

        return $discountTotal;
    }
}
