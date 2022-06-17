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

namespace Mageplaza\GiftCard\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCard\Model\GiftCard\Action;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Model\TransactionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class CreditmemoSaveAfter
 * @package Mageplaza\GiftCard\Observer
 */
class CreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var Product
     */
    protected $_productHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * CreditmemoSaveAfter constructor.
     *
     * @param Product $productHelper
     * @param LoggerInterface $logger
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        Product $productHelper,
        LoggerInterface $logger,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory
    ) {
        $this->_productHelper     = $productHelper;
        $this->logger             = $logger;
        $this->giftCardFactory    = $giftCardFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (!$creditmemo->getRefundGiftCardFlag()) {
            return $this;
        }

        /** @var Creditmemo\Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            /** @var Item $orderItem */
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy() || $orderItem->getProductType() !== GiftCard::TYPE_GIFTCARD) {
                continue;
            }

            $this->_productHelper->refundGiftCode($orderItem, $item->getQty());
        }

        /** @var Order $order */
        $order = $creditmemo->getOrder();

        if ($this->_productHelper->allowRefundGiftCard()) {
            /** Refund Gift Cards */
            $giftCards = $creditmemo->getGiftCards() ?: [];
            foreach ($giftCards as $code => $amount) {
                try {
                    $giftCard = $this->giftCardFactory->create()
                        ->loadByCode($code);
                    if ($giftCard->getId()) {
                        $giftCard->addBalance($amount)
                            ->setAction(Action::ACTION_REFUND)
                            ->setActionVars(['order_increment_id' => $order->getIncrementId()])
                            ->save();
                    }
                } catch (Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

        /** Refund Gift Credit */
        $giftCredit = $creditmemo->getBaseGiftCreditAmount();
        if (abs($giftCredit) > 0.0001) {
            try {
                $this->transactionFactory->create()
                    ->createTransaction(
                        \Mageplaza\GiftCard\Model\Transaction\Action::ACTION_REFUND,
                        abs($giftCredit),
                        $order->getCustomerId(),
                        ['order_increment_id' => $order->getIncrementId()]
                    );
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $this;
    }
}
