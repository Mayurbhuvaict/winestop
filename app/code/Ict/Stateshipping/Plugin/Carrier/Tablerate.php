<?php
/**
 * @author Ict Team
 * @copyright Copyright (c) 2018 Ict (http://icreativetechnologies.com/)
 * @package Ict_Stateshipping
 */
namespace Ict\Stateshipping\Plugin\Carrier;

class Tablerate
{
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    
    public function afterCollectRates(
        \Magento\OfflineShipping\Model\Carrier\Tablerate $subject,
        $result,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $enable = $this->scopeConfig->getValue('stateshipping/general/enable');
        if ($enable == false) {
            return $result;
        }
        
        $address = json_decode($request->toJson());
        
        if ($address) {
            $requestCountry = $address->dest_country_id;
            $requestState = $address->dest_region_id;
        
            $stateShipping = $this->scopeConfig->getValue(
                'carriers/tablerate/specificstate',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $countryShipping = $this->scopeConfig->getValue(
                'carriers/tablerate/ictspecificcountry',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $shipAll = $this->scopeConfig->getValue(
                'carriers/tablerate/sallowspecific',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $adminShipArray = explode(",", $stateShipping);
            
            if (!in_array($requestState, $adminShipArray) || $shipAll == 0 || $requestCountry != $countryShipping) {
                return false;
            }
        }

        if ($subject) {
            return $result;
        }
        return $result;
    }
}
