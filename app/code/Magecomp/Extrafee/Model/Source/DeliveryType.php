<?php 
namespace Magecomp\Extrafee\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\RegionFactory;

class DeliveryType
{
    const STOREPICKUP_CODE = 'storepickup';
    const SHIP_CODE = 'ship';
    const SHIP_CODE_LABEL = 'SHIP';
    const SHIP_CODE_LOCAL = 'local';
    const LOCAL_CODE_LABEL = 'LOCAL';
    const REGION_CODE = 'code';
    const ADDRESS_BUSSINESS_TEXT = 'business';
    const LOCAL_DELIVERY_STREET_ADDR = 'general/store_information/street_line1';
    const LOCAL_DELIVERY_CITY = 'general/store_information/city';
    const LOCAL_DELIVERY_REGION = 'general/store_information/region_id';
    const LOCAL_DELIVERY_ZIP_CODE = 'general/store_information/postcode';

    protected $scopeConfig;
    protected $shippingmodelconfig;
    public function __construct(
        Config $shippingmodelconfig, 
        ScopeConfigInterface $scopeConfig,  
        RegionFactory $regionFactory
    ) {
        $this->shippingmodelconfig = $shippingmodelconfig;
        $this->scopeConfig = $scopeConfig;
        $this->regionFactory = $regionFactory;
 }

 public function toOptionArray()
 {
    $methods = array();
    $shippings = $this->shippingmodelconfig->getActiveCarriers();
    $regionId = $this->scopeConfig->getValue(self::LOCAL_DELIVERY_REGION, ScopeInterface::SCOPE_STORE);
    $regionData = $this->regionFactory->create()->load($regionId);
    $data = 'STORE PICKUP/CURBSIDE - STORE LOCATION : ';
    $citydata = 'LOCAL DELIVERY (Burlingame, Hillsborough, San Mateo, Millbrae, Belmont, San Carlos & San Bruno)';
    $data .= $this->scopeConfig->getValue(self::LOCAL_DELIVERY_STREET_ADDR, ScopeInterface::SCOPE_STORE).', ';
    $data .= $this->scopeConfig->getValue(self::LOCAL_DELIVERY_CITY, ScopeInterface::SCOPE_STORE).', ';
    $data .= $regionData->getData(self::REGION_CODE).' ';
    $data .= $this->scopeConfig->getValue(self::LOCAL_DELIVERY_ZIP_CODE, ScopeInterface::SCOPE_STORE);

    $methods = [
        ['value'=>self::STOREPICKUP_CODE, 'label'=>$data],
        ['value'=>self::SHIP_CODE_LOCAL, 'label'=>$citydata],
        ['value'=>self::SHIP_CODE, 'label'=>self::SHIP_CODE_LABEL]
    ];
    return $methods;
 }
}
?>