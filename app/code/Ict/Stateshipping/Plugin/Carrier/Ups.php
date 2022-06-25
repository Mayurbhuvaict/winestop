<?php
/**
 *
 * @author Ict Team
 * @copyright Copyright (c) 2018 Ict (http://icreativetechnologies.com/)
 * @package Ict_Stateshipping
 */
namespace Ict\Stateshipping\Plugin\Carrier;

class Ups
{
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    
    public function afterCollectRates(
        \Magento\Ups\Model\Carrier $subject,
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
        
            $stateShipping = $this->scopeConfig->getValue('carriers/ups/specificstate');
            $countryShipping = $this->scopeConfig->getValue('carriers/ups/ictspecificcountry');
            $shipAll = $this->scopeConfig->getValue('carriers/ups/sallowspecific');

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
