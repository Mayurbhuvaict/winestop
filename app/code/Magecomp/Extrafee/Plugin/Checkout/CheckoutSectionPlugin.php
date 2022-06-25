<?php

namespace Magecomp\Extrafee\Plugin\Checkout;

use Psr\Log\LoggerInterface;
use Magento\Quote\Api\Data\AddressInterface;

class CheckoutSectionPlugin 
{
     /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * ShippingAddressManagementPlugin constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     */
    public function beforeGetSectionsDetails(
        \Aheadworks\OneStepCheckout\Model\CheckoutSectionsManagement $subject,
        $cartId,
        $sections,
        AddressInterface $shippingAddress = null,
        AddressInterface $billingAddress = null,
        $negotiableQuoteId = null
    ) {
        if($shippingAddress){
            $extAttributes = $shippingAddress->getExtensionAttributes();
            if (!empty($extAttributes)) {
                try {
                    $shippingAddress->setType($extAttributes->getType());
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$cartId, $sections, $shippingAddress, $billingAddress, $negotiableQuoteId];
    }
}