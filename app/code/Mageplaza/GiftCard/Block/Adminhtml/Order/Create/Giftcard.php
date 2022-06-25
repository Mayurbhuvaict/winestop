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

namespace Mageplaza\GiftCard\Block\Adminhtml\Order\Create;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\Sales\Model\AdminOrder\Create;
use Mageplaza\GiftCard\Helper\Checkout as CheckoutHelper;

/**
 * Class Giftcard
 * @package Mageplaza\GiftCard\Block\Adminhtml\Order\Create
 */
class Giftcard extends AbstractCreate
{
    /**
     * @var CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * @var Format
     */
    protected $_localeFormat;

    /**
     * @var int|double|float|null
     */
    protected $maxCreditUsed;

    /**
     * @var int|double|float|null
     */
    protected $customerBalance;

    /**
     * @var null|boolean
     */
    protected $canUseGiftCard;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param CheckoutHelper $checkoutHelper
     * @param Format $localeFormat
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        CheckoutHelper $checkoutHelper,
        Format $localeFormat,
        array $data = []
    ) {
        $this->_checkoutHelper = $checkoutHelper;
        $this->_localeFormat   = $localeFormat;

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_gift_card_coupons_form');
    }

    /**
     * @return bool|null
     */
    protected function canUseGiftCard()
    {
        if ($this->canUseGiftCard === null) {
            $this->canUseGiftCard = $this->_checkoutHelper->canUsedGiftCard($this->getQuote());
        }

        return $this->canUseGiftCard;
    }

    /**
     * @return bool
     */
    public function enableGiftCard()
    {
        return !$this->_checkoutHelper->isUsedCouponBox() && $this->canUseGiftCard();
    }

    /**
     * Enable Apply Credit
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function enableCredit()
    {
        return $this->_checkoutHelper->canUsedCredit() && $this->canUseGiftCard() && (boolean) $this->getMaxUsed();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBalanceFormatted()
    {
        return $this->formatPrice($this->getCustomerBalance());
    }

    /**
     * get credit amount data for slider
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCreditData()
    {
        return $this->escapeHtml(CheckoutHelper::jsonEncode([
            'creditUsed'  => $this->_checkoutHelper->getGiftCreditUsed($this->getQuote()),
            'maxUsed'     => $this->getMaxUsed(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(null, $this->getQuote()->getQuoteCurrencyCode())
        ]));
    }

    /**
     * @return float|int|null|string
     * @throws NoSuchEntityException
     */
    protected function getCustomerBalance()
    {
        if (!$this->customerBalance) {
            $this->customerBalance = $this->_checkoutHelper->getCustomerBalance($this->getCustomerId());
        }

        return $this->customerBalance;
    }

    /**
     * @return float|int|mixed|null
     * @throws NoSuchEntityException
     */
    protected function getMaxUsed()
    {
        if (!$this->maxCreditUsed) {
            $balance    = $this->getCustomerBalance();
            $orderTotal = $this->_checkoutHelper->getTotalAmountForDiscount($this->getQuote(), true);

            $this->maxCreditUsed = min($orderTotal, $balance);
        }

        return $this->maxCreditUsed;
    }

    /**
     * Get All codes applied
     *
     * @return array|null
     */
    public function getGiftCodesUsed()
    {
        return $this->_checkoutHelper->getGiftCardsUsed();
    }
}
