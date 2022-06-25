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
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Model\Source\GenerateGiftCodeEvent;

/**
 * Class InvoiceSaveAfter
 * @package Mageplaza\GiftCard\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
{
    /**
     * @var Product
     */
    protected $_helper;

    /**
     * InvoiceSaveAfter constructor.
     *
     * @param Product $helper
     */
    public function __construct(Product $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$this->_helper->isEnabled() ||
            !$this->_helper->isGenerateCode(GenerateGiftCodeEvent::INVOICED) ||
            $invoice->getState() !== Invoice::STATE_PAID
        ) {
            return $this;
        }

        /** @var Invoice\Item $item */
        foreach ($invoice->getAllItems() as $item) {
            /** @var Item $orderItem */
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy() || $orderItem->getProductType() !== GiftCard::TYPE_GIFTCARD) {
                continue;
            }

            $this->_helper->generateGiftCode($invoice->getOrder(), $orderItem, $item->getQty());
        }

        return $this;
    }
}
