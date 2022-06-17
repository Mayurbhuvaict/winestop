<?php

namespace Magecomp\Extrafee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magecomp\Extrafee\Model\Source\AddressType;

class Data extends AbstractHelper
{
    /**
     * Custom fee config path
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'Extrafee/Extrafee/status';
    const CONFIG_CUSTOM_FEE = 'Extrafee/Extrafee/Extrafee_amount';
    const CONFIG_FEE_LABEL = 'Extrafee/Extrafee/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'Extrafee/Extrafee/minimum_order_amount';
    const XML_PATH_FOR_HANDLING_LESS_ONE = 'Extrafee/Extrafee/less_one_liter';
    const XML_PATH_FOR_HANDLING_LESS_TWO = 'Extrafee/Extrafee/less_two_liter';
    
    const GLS_METHODS = 'Extrafee/gls/gls_methods';
    const GLS_BUSSINES_RATE = 'Extrafee/gls/gls_business/gls_business_rate';
    const GLS_BUSSINES_ADULT_FEE = 'Extrafee/gls/gls_business/gls_business_adultFee';
    
    const GLS_RESIDENT_RATE = 'Extrafee/gls/gls_residential/gls_residential_rate';
    const GLS_RESIDENT_ADULT_FEE = 'Extrafee/gls/gls_residential/gls_residential_adultFee';

    const FEDEX_GROUND_METHODS = 'Extrafee/fedex_ground/fedex_ground_methods';
    const FEDEX_GROUND_BUSSINES_RATE = 'Extrafee/fedex_ground/fedex_business_ground/fedex_business_ground_rate';
    const FEDEX_GROUND_BUSSINES_ADULT_FEE = 'Extrafee/fedex_ground/fedex_business_ground/fedex_business_ground_adultFee';
    
    const FEDEX_GROUND_RESIDENT_RATE = 'Extrafee/fedex_ground/fedex_residential_ground/fedex_residential_ground_rate';
    const FEDEX_GROUND_RESIDENT_ADULT_FEE = 'Extrafee/fedex_ground/fedex_residential_ground/fedex_residential_ground_adultFee';

    const FEDEX_AIR_METHODS = 'Extrafee/fedex_air/fedex_air_methods';
    const FEDEX_AIR_RESIDENT_RATE = 'Extrafee/fedex_air/fedex_residential_air/fedex_residential_air_rate';
    const FEDEX_AIR_RESIDENT_ADULT_FEE = 'Extrafee/fedex_air/fedex_residential_air/fedex_residential_air_adultFee';
    
    const ADDITIONAL_FEE = 'Extrafee/additional/fee';

    protected $_handlingConfig=[];
    protected $_additional=[];
    protected $_rates;
    protected $_glsRates;
    protected $_allowOptions = ['700ml','720ml','750ml','750ml','750cl','1 Liter','1.5 Liter'];
    protected $_methodsCollection;
    /**
     * @return mixed
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Meetanshi\ShippingRates\Model\ResourceModel\Method\Collection $methodCollection,
		\Magecomp\Extrafee\Model\Source\AddressType $addressType
    )
    {
        parent::__construct($context);
        $this->_methodCollection = $methodCollection;
		$this->_addressType = $addressType;
    }

	public function getAddressTypeData()
    {
		$AddressType = array();		
		if(count($this->_addressType->getAllOptions())){
			return $this->_addressType->getAllOptions();
		}		
		return $AddressType;
	}
	public function getDefaultAddressType()
    {
		return $this->_addressType::ADDRESS_RESIDENTIAL_TEXT;
	}
	public function getAddressTypeCode()
    {
		return $this->_addressType::ADDRESS_TYPE_CODE;
	}

    public function isModuleEnabled()
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getExtrafee($quote)
    {
        return $this->getHandlingFee($quote);
        /*$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope);*/
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getFeeLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_MINIMUM_ORDER_AMOUNT, $storeScope);
    }

    public function getConfigValue($configPath, $store = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
 
    /**
     * Get serialized config value
     * temporarily solution to get unserialized config value
     * should be deprecated in 2.3.x
     *
     * @param $configPath
     * @param null $store
     * @return mixed
     */
    public function getSerializedConfigValue($configPath, $store = null)
    {
        $value = $this->getConfigValue($configPath, $store);
 
        if (empty($value)) return false;
 
        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }
 
        return $unserializer->unserialize($value);
    }
 
    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    public function isEnable(){
        return $this->getConfigValue(self::CONFIG_CUSTOM_IS_ENABLED);
    }

    public function getHandlingConfig(){
        if(count($this->_handlingConfig)){
            return $this->_handlingConfig;
        }

        $handlingLessOne = $this->getSerializedConfigValue(self::XML_PATH_FOR_HANDLING_LESS_ONE);
        $handlingLessTwo = $this->getSerializedConfigValue(self::XML_PATH_FOR_HANDLING_LESS_TWO);
        $prepareData = [];
        foreach ($handlingLessOne as $key => $_row) {
            if(strpos(trim($_row['box']), '-')){
                $split = explode('-', trim($_row['box']));
                if(count($split)==2){
                    if($split[0]<$split[1]){
                        for ($i=$split[0]; $i <=$split[1]; $i++) { 
                            $prepareData[1][$i] = ['box'=>$i, 'fee'=>$_row['fee'],'weight'=>$_row['weight']];
                        }
                    }
                }
            }
            else{
                $prepareData[1][$_row['box']]=$_row;
            }
        }
        foreach ($handlingLessTwo as $key => $_row) {
            if(strpos(trim($_row['box']), '-')){
                $split = explode('-', trim($_row['box']));
                if(count($split)==2){
                    if($split[0]<$split[1]){
                        for ($i=$split[0]; $i <=$split[1]; $i++) { 
                            $prepareData[2][$i] = ['box'=>$i, 'fee'=>$_row['fee'],'weight'=>$_row['weight']];
                        }
                    }
                }
            }
            else{
                $prepareData[2][$_row['box']]=$_row;
            }
        }
        $this->_handlingConfig = $prepareData;
        return $this->_handlingConfig;
    }

    public function getHandlingFee($quote){
        $handlingFee = 0;
        try{
            if($this->isEnable() && $quote && $quote->getId()){
                $handling = $this->getHandlingConfig();
                $items = $quote->getAllVisibleItems();
                $existedHandling = [1=>0,2=>0];
                if($items){
                    foreach ($items as $key => $_item) {
                        $product = ObjectManager::getInstance()->create('Magento\Catalog\Model\Product');
                        $id = $product->getIdBySku($_item->getSku());
                        if($id){
                            $_pro = $product->setStoreId($_item->getStoreId())->load($id);
                            if($_pro && $_pro->getId()){
                                $attr = $_pro->getResource()->getAttribute('bottle_size');
                                if ($attr->usesSource()) {
                                    $optionId = $_pro->getBottleSize();
                                    $optionText = $attr->getSource()->getOptionText($optionId);
                                    if($optionText && in_array($optionText, $this->_allowOptions))
                                    {
                                        if($optionText=='1.5 Liter'){
                                            $existedHandling[2] = $existedHandling[2]+$_item->getQty();
                                        }else{
                                            $existedHandling[1] = $existedHandling[1]+$_item->getQty();

                                        }
                                    }
                                }
                            }
                        }
                    }
                        
                    $lastBelowOne = isset($handling[1]) ? end($handling[1]):[];
                    $lastBelowTwo = isset($handling[2]) ? end($handling[2]):[];

                    if($existedHandling[1]>0 && isset($handling[1]) && isset($handling[1][$existedHandling[1]])){
                        $_hand = $handling[1][$existedHandling[1]];
                        $handlingFee+=(float)$_hand['fee'];
                    }elseif($existedHandling[1] && count($lastBelowOne)){
                        $_hand = isset($handling[1][$existedHandling[1]])?$handling[1][$existedHandling[1]]:[];
                        $rest = intval($existedHandling[1]%$lastBelowOne['box']);
                        $_rest = intval($existedHandling[1]/$lastBelowOne['box']);
                        $handlingFee+=(float)$lastBelowOne['fee']*(float)$_rest;
                        
                        if(isset($handling[1][$rest]) && !count($_hand)){
                            $handlingFee+=(float)$handling[1][$rest]['fee'];
                        }
                        if(isset($handling[1][$rest]) && count($_hand)){
                            $handlingFee+=(float)$_hand['fee'];
                        }
                    }

                    if($existedHandling[2]>0 && isset($handling[2]) && isset($handling[2][$existedHandling[2]])){
                        $_hand = $handling[2][$existedHandling[2]];
                        $handlingFee+=(float)$_hand['fee'];
                    }elseif($existedHandling[2] && count($lastBelowTwo)){
                        $_hand = isset($handling[2][$existedHandling[2]])?$handling[2][$existedHandling[2]]:[];
                        $rest = intval($existedHandling[2]%$lastBelowTwo['box']);
                        $_rest = intval($existedHandling[2]/$lastBelowTwo['box']);

                        $handlingFee+=(float)$lastBelowTwo['fee']*(float)$_rest;
                        
                        if(isset($handling[2][$rest]) && !count($_hand)){
                            $handlingFee+=(float)$handling[2][$rest]['fee'];
                        }
                        if(isset($handling[2][$rest]) && count($_hand)){
                            $handlingFee+=(float)$_hand['fee'];
                        }
                    }
                }
            }
        }catch(\Exception $e){}
        return $handlingFee;
    }

    public function getShippingRatesAdultPrice(){
        if($this->_rates){
            return $this->_rates;
        }
        
        $rates = [];
        
        $glsMethods = explode(',',$this->getConfigValue(self::GLS_METHODS));
        if($glsMethods){
            foreach($glsMethods as $_methodId){
                $rates[$_methodId][AddressType::ADDRESS_BUSSINESS_TEXT]['rate']=floatval($this->getConfigValue(self::GLS_BUSSINES_RATE));
                $rates[$_methodId][AddressType::ADDRESS_BUSSINESS_TEXT]['adultfee']=floatval($this->getConfigValue(self::GLS_BUSSINES_ADULT_FEE));

                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['rate']=floatval($this->getConfigValue(self::GLS_RESIDENT_RATE));
                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['adultfee']=floatval($this->getConfigValue(self::GLS_RESIDENT_ADULT_FEE));
            }
        }

        $fedxGroundMethods = explode(',',$this->getConfigValue(self::FEDEX_GROUND_METHODS));
        if($fedxGroundMethods){
            foreach($fedxGroundMethods as $_methodId){                
                $rates[$_methodId][AddressType::ADDRESS_BUSSINESS_TEXT]['rate']=floatval($this->getConfigValue(self::FEDEX_GROUND_BUSSINES_RATE));
                $rates[$_methodId][AddressType::ADDRESS_BUSSINESS_TEXT]['adultfee']=floatval($this->getConfigValue(self::FEDEX_GROUND_BUSSINES_ADULT_FEE));

                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['rate']=floatval($this->getConfigValue(self::FEDEX_GROUND_RESIDENT_RATE));
                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['adultfee']=floatval($this->getConfigValue(self::FEDEX_GROUND_RESIDENT_ADULT_FEE));
            }
        }

        $fedxAirMethods = explode(',',$this->getConfigValue(self::FEDEX_AIR_METHODS));
        if($fedxAirMethods){
            foreach($fedxAirMethods as $_methodId){                
                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['rate']=floatval($this->getConfigValue(self::FEDEX_AIR_RESIDENT_RATE));
                $rates[$_methodId][AddressType::ADDRESS_RESIDENTIAL_TEXT]['adultfee']=floatval($this->getConfigValue(self::FEDEX_AIR_RESIDENT_ADULT_FEE));
            }
        }
        
        $this->_rates = $rates;
        return $this->_rates;
    }

    public function getAdditionalFee(){
        if(count($this->_additional)){
            return $this->_additional;
        }

        $additional = $this->getSerializedConfigValue(self::ADDITIONAL_FEE);
        $rates=[];
        if(is_array($additional) && count($additional)){
            foreach($additional as $fee){
                if(isset($fee['method_id']) && isset($fee['additional_fee'])){
                    if($fee['method_id'] && $fee['additional_fee']){
                        $collection = clone $this->_methodCollection;
                        $collection->addFieldToFilter('category_id',$fee['method_id']);
                        foreach($collection as $method){
                            $rates[$method->getId()] = floatval($fee['additional_fee']);
                        }
                    }
                }
            }
        }
        $this->_additional = $rates;
        return $this->_additional;
    }

    /*public function getGlsAdultPrice(){
        if($this->_glsRates){
            return $this->_glsRates;
        }
        
        $rates = [];
        $rates[AddressType::ADDRESS_BUSSINESS_TEXT]['rate']=floatval($this->getConfigValue(self::GLS_BUSSINES_RATE));
        $rates[AddressType::ADDRESS_BUSSINESS_TEXT]['adultfee']=floatval($this->getConfigValue(self::GLS_BUSSINES_ADULT_FEE));

        $rates[AddressType::ADDRESS_RESIDENTIAL_TEXT]['rate']=floatval($this->getConfigValue(self::GLS_RESIDENT_RATE));
        $rates[AddressType::ADDRESS_RESIDENTIAL_TEXT]['adultfee']=floatval($this->getConfigValue(self::GLS_RESIDENT_ADULT_FEE));

        $this->_glsRates = $rates;
        return $this->_glsRates;
    }*/
}
