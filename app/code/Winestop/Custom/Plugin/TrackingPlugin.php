<?php
	
namespace Winestop\Custom\Plugin;

class TrackingPlugin
{
	protected $_carrierFactory;
	
	public function __construct(
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    ) {
        $this->_carrierFactory = $carrierFactory;
    }

	public function aroundGetNumberDetail(
		\Magento\Shipping\Model\Order\Track $subject,
		\Closure $proceed
	) {
		$carrierInstance = $this->_carrierFactory->create($subject->getCarrierCode());
		$trackingInfo = $carrierInstance->getTrackingInfo($subject->getNumber());
		$result = $proceed();
		if (!$trackingInfo) {
			return (string)__('No detail for number "%1"', $subject->getNumber());
		}
		return $result;
	}
}