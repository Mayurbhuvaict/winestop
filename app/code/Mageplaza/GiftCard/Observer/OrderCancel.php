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
use Mageplaza\GiftCard\Helper\Data as Helper;
use Mageplaza\GiftCard\Model\GiftCard\Action;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\TransactionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class OrderCancel
 * @package Mageplaza\GiftCard\Observer
 */
class OrderCancel implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

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
     * OrderCancel constructor.
     *
     * @param Helper $helper
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        LoggerInterface $logger
    ) {
        $this->_helper            = $helper;
        $this->giftCardFactory    = $giftCardFactory;
        $this->transactionFactory = $transactionFactory;
        $this->logger             = $logger;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $giftCards = $order->getMpGiftCards() ? Helper::jsonDecode($order->getMpGiftCards()) : [];
        foreach ($giftCards as $code => $amount) {
            try {
                $giftCard = $this->giftCardFactory->create()->loadByCode($code);
                if ($giftCard->getId()) {
                    $giftCard->addBalance($amount)
                        ->setAction(Action::ACTION_REVERT)
                        ->setActionVars(['order_increment_id' => $order->getIncrementId()])
                        ->save();
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        $giftCredit = $order->getBaseGiftCreditAmount();
        if (abs($giftCredit) > 0.0001) {
            try {
                $this->transactionFactory->create()
                    ->createTransaction(
                        \Mageplaza\GiftCard\Model\Transaction\Action::ACTION_REVERT,
                        $giftCredit,
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
