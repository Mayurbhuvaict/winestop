<?php
namespace Magecomp\Extrafee\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     */
    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService
    ) {
        $this->objectCopyService = $objectCopyService;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return SaveOrderBeforeSalesModelQuoteObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $shippingAddressData = $quote->getShippingAddress()->getData();
        if (isset($shippingAddressData['type'])) {
            $order->getShippingAddress()->setType($shippingAddressData['type']);
        }

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote_address',
            'to_order_address',
            $quote,
            $order
        );
        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote_address',
            'to_customer_address',
            $quote,
            $order
        );

        return $this;
    }
}