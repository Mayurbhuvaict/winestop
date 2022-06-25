<?php

namespace Magecomp\Extrafee\Plugin\Checkout;

use Psr\Log\LoggerInterface;
use Magento\Quote\Model\ShippingAddressManagement;
use Magento\Quote\Api\Data\AddressInterface;

class ShippingAddressManagementPlugin 
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
     * @param ShippingAddressManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     */
    public function beforeAssign(
        ShippingAddressManagement $subject,
        $cartId,
        AddressInterface $address
    ) {
        if($address){
            $extAttributes = $address->getExtensionAttributes();
            if (!empty($extAttributes)) {
                try {
                    $address->setType($extAttributes->getType());
                    
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$cartId, $address];
    }
}