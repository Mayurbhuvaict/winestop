<?php

namespace Magecomp\Extrafee\Plugin;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Directory\Model\RegionFactory;

class DeliveryConfigProvider
{
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
    protected $regionFactory;

    public function __construct (
        RegionFactory $regionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->regionFactory = $regionFactory;
    }

    public function afterGetConfig(\Aheadworks\OneStepCheckout\Model\ConfigProvider $subject, $config) {
        
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $regionId = $this->scopeConfig->getValue('general/store_information/region_id', $storeScope);
        $region = $this->regionFactory->create()->load($regionId);
        
        $config['delivery_address'] = [
                'firstname'=>'WINE',
                'lastname'=>'STOP',
                'company'=>'',
                'street'=>
                [
                    $this->scopeConfig->getValue('general/store_information/street_line1', $storeScope),
                    $this->scopeConfig->getValue('general/store_information/street_line2', $storeScope)
                ],
                'city'  => $this->scopeConfig->getValue('general/store_information/city', $storeScope),
                'postcode'  => $this->scopeConfig->getValue('general/store_information/postcode', $storeScope),
                'country_id'    => $this->scopeConfig->getValue('general/store_information/country_id', $storeScope),
                'region_id'    => $regionId,
                'region'    => ($region) ? $region->getName() : 'California',
                'telephone'    => $this->scopeConfig->getValue('general/store_information/phone', $storeScope),
            ];
        return $config;
    }
}
