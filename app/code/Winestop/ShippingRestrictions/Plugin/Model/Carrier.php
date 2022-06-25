<?php
namespace Winestop\ShippingRestrictions\Plugin\Model;

/**
 * 
 */
class Carrier
{
	
   /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;

   /**
   * fedex Allow State.
   */
   const XML_PATH_FEDEX_ALLOW_STATE = 'shippingRestrictions/general/fedex_allow_state';

   public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
   {
      $this->scopeConfig = $scopeConfig;
   }

	public function afterCollectRates(\Magento\Fedex\Model\Carrier $subject, $result, $request){       
        
		$fedex_allow_state = $this->scopeConfig->getValue(
                self::XML_PATH_FEDEX_ALLOW_STATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
		$fedex_allow_state_array = $wsb_array = explode(",", $fedex_allow_state);
		$dest_region_code = $request->getData('dest_region_code');
		if (!in_array($dest_region_code, $fedex_allow_state_array)) {
			$result = false;
		} 
        return $result;
    }
}