<?php

namespace Magecomp\Extrafee\Plugin\Checkout;

use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInfoManagementPlugin 
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
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $shippingInfo
    ) {
        $address = $shippingInfo->getShippingAddress();
        if($address){
            $extAttributes = $address->getExtensionAttributes();
            if (!empty($extAttributes)) {
                try {
                    $address->setType($extAttributes->getType());
                    //$shippingInfo->setShippingAddress($address);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$cartId, $shippingInfo];
    }
}