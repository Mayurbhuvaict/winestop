<?php
namespace Winestop\Custom\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Winestop\Custom\Registry\CurrentProduct;

/**
 * Class RegisterCurrentProductObserver
 *
 * Current product observer
 */
class RegisterCurrentProductObserver implements ObserverInterface
{
    /**
     * @var CurrentProduct
     */
    private $currentProduct;

    /**
     * RegisterCurrentProductObserver constructor.
     *
     * @param CurrentProduct $currentProduct
     */
    public function __construct(
        CurrentProduct $currentProduct
    ) {
        $this->currentProduct = $currentProduct;
    }

    /**
     * Trigger event
     *
     * @param Event $event
     */
    public function execute(Event $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData('product');
        $this->currentProduct->set($product);
    }
}