<?php
namespace Winestop\Gso\Observer;
/**
 * 
 */
class Ordership implements \Magento\Framework\Event\ObserverInterface
{
	
	/**
   * @var \Winestop\Gso\Helper\Data
   */
   protected $dataHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $_convertOrder;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $_shipmentNotifier;

    protected $trackFactory;

    /**
     * @param Context                                     $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Convert\Order          $convertOrder
     * @param \Magento\Shipping\Model\ShipmentNotifier    $shipmentNotifier
     */
   public function __construct(
        \Magento\Sales\Model\Order $orderRepository,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
   	    \Winestop\Gso\Helper\Data $dataHelper,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
   )
   {
      $this->_orderRepository = $orderRepository;
      $this->_convertOrder = $convertOrder;
      $this->_shipmentNotifier = $shipmentNotifier;      
      $this->dataHelper = $dataHelper;
      $this->trackFactory = $trackFactory;
   }

	public function execute(\Magento\Framework\Event\Observer $observer)
    {
      $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
      //$order = $observer->getEvent()->getOrder();
      if ($order->getData('shipping_method') == 'gsoshipping_gsoshipping') {
        $_shippingAddress = $order->getShippingAddress();
        $AccountNumber = $this->dataHelper->getGsoAccountno();
        $ServiceCode = $this->dataHelper->getGsoServiceCode();     
        $trackingnumber = (string) $order->getData('quote_id') . $order->getData('increment_id');
        $ShipToCompany = "THE WINE STOP";
        if ($_shippingAddress->getData('company') != '') {
          $ShipToCompany = $_shippingAddress->getData('company');
        }
        $DeliveryAddress1 = $_shippingAddress->getData('street');   
        $DeliveryCity = $_shippingAddress->getData('city');
        $DeliveryState = (string)$this->dataHelper->getShippingregion($_shippingAddress->getData('region_id'));
        $DeliveryZip = $_shippingAddress->getData('postcode');
        $Shipment = array(
                "TrackingNumber" => $trackingnumber,
                "ShipperCompany" => $this->dataHelper->getStoreName(),
                "PickupAddress1" => $this->dataHelper->getStoreStreet(),
                "PickupCity" => $this->dataHelper->getStoreCity(),
                "PickupState" => $this->dataHelper->getStoreStateCode(),
                "PickupZip" => $this->dataHelper->getStoreZip(),
                "ShipToCompany" => $ShipToCompany,
                "DeliveryAddress1" => $DeliveryAddress1,
                "DeliveryCity" => $DeliveryCity,
                "DeliveryState" => $DeliveryState,
                "DeliveryZip" => $DeliveryZip,
                "ServiceCode" => $ServiceCode
              ); 
        $Request = array(
                    'AccountNumber' => $AccountNumber,
                    "Shipment" => $Shipment                
                    );
         
        $response = $this->dataHelper->getGsoShipment($Request);
        $response = json_decode($response, true);
        if ($response['StatusCode'] == '200') {
          $tracking_number = (string)$response['TrackingNumber'];
          $data = array(
                'carrier_code' => 'custom',
                'title' => 'Golden State Overnight',
                'number' => $tracking_number, // Replace with your tracking number
            );            
            try {
                // Save created Order Shipment
                $track = $this->trackFactory->create()->addData($data);
                $shipment->addTrack($track)->save();
                $shipment->save();
                $shipment->getOrder()->save();
                
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
                );
            }
        }               
      }     
    }   
}