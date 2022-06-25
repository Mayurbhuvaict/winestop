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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftCard\Api\Data\RedeemDetailInterfaceFactory;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Checkout as CheckoutHelper;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\TransactionFactory;

/**
 * Class GiftCardManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class GiftCardManagement implements GiftCardManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @var CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var RedeemDetailInterfaceFactory
     */
    private $redeemDetailFactory;

    /**
     * GiftCardManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftCardFactory $giftCardFactory
     * @param CheckoutHelper $checkoutHelper
     * @param TransactionFactory $transactionFactory
     * @param RedeemDetailInterfaceFactory $redeemDetailFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GiftCardFactory $giftCardFactory,
        CheckoutHelper $checkoutHelper,
        TransactionFactory $transactionFactory,
        RedeemDetailInterfaceFactory $redeemDetailFactory
    ) {
        $this->quoteRepository     = $quoteRepository;
        $this->_checkoutHelper     = $checkoutHelper;
        $this->_giftCardFactory    = $giftCardFactory;
        $this->transactionFactory  = $transactionFactory;
        $this->redeemDetailFactory = $redeemDetailFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $code)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $this->_checkoutHelper->addGiftCards($code, $quote);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift card %1', $code));
        }

        $giftCardUsed = $this->_checkoutHelper->getGiftCardsUsed($quote);
        if (!array_key_exists($code, $giftCardUsed)) {
            throw new NoSuchEntityException(__('Gift Card is not valid'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $code)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $giftCard = $this->_giftCardFactory->create();
        $giftCard->load($code);
        if (!$giftCard->getId()) {
            $giftCard->loadByCode($code);
        }
        if (!$giftCard->getId()) {
            throw new CouldNotDeleteException(__('Could not cancel gift card'));
        }

        $code = $giftCard->getCode();

        $giftCardUsed = $this->_checkoutHelper->getGiftCardsUsed($quote);
        if (!array_key_exists($code, $giftCardUsed)) {
            throw new NoSuchEntityException(__('Could not cancel gift card'));
        }

        try {
            $this->_checkoutHelper->removeGiftCard($code, false, $quote);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not cancel gift card'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function credit($cartId, $amount)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $this->_checkoutHelper->applyCredit($amount, $quote);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift credit'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function redeem($customerId, $code)
    {
        $giftCard = $this->_giftCardFactory->create()->load($code, 'code');

        if (!$giftCard->canRedeem()) {
            throw new LocalizedException(__('Gift Card "%1" cannot be redeemed.', $code));
        }

        $customer = $this->_checkoutHelper->getCustomer($customerId);

        $this->transactionFactory->create()->redeemGiftCard($customer, $giftCard);

        return $this->redeemDetailFactory->create()
            ->setCustomerBalance($this->_checkoutHelper->getCustomerBalance($customer));
    }
}
