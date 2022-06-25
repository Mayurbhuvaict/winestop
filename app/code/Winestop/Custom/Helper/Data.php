<?php 
namespace Winestop\Custom\Helper;

use Webkul\OutOfStockNotification\Helper\Data as MainHelper;
/**
 * 
 */
class Data extends MainHelper
{
	protected $regionFactory;

	/**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
    protected $scopeConfig;

    protected $stockRegistry;

    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->regionFactory = $regionFactory;
        $this->scopeConfig = $scopeConfig; 
    }
	
	public function generateTemplate(
        $emailTemplateVariables,
        $senderInfo,
        $receiverInfo,
        $replyTo = ['email' => '', 'name'=> '']
    ) {
    
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state =  $objectManager->get('Magento\Framework\App\State');  
		if ($state->getAreaCode() == 'frontend') {
				$template = $this->transportBuilder
	                ->setTemplateIdentifier($this->_template)
	                ->setTemplateOptions(
	                    [
	                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
	                        'store' => $this->storeManager->getStore()->getId(),
	                    ]
	                )
	                ->setTemplateVars($emailTemplateVariables)
	                ->setFrom($senderInfo)
	                ->addTo($receiverInfo['email'], $receiverInfo['name']);
	                
	        if ($replyTo['email'] != "") {
	            $template->setReplyTo($replyTo['email'], $replyTo['name']);
	        }

	        return $this;
		} else {      
	        $template = $this->transportBuilder
	                ->setTemplateIdentifier($this->_template)
	                ->setTemplateOptions(
	                    [
	                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
	                        'store' => $this->storeManager->getStore()->getId(),
	                    ]
	                )
	                ->setTemplateVars($emailTemplateVariables)
	                ->setFrom($senderInfo)
	                ->addTo($receiverInfo['email']);
	                
	        if ($replyTo['email'] != "") {
	            $template->setReplyTo($replyTo['email'], $replyTo['name']);
	        }

	        return $this;
	    }
    }
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStateDetail($code)
    {
    	if (is_numeric($code)) {
    		$region = $this->regionFactory->create()->load($code);
    	}
    	else
    	{    		
    		$region = $this->regionFactory->create();
        	$region = $region->loadByCode($code, 'US');
    	}
        return $region;
    }
    public function isNewArrival($product)
    {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate   = $product->getNewsToDate();
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));
        $startDate = date('Y-m-d', strtotime($newsFromDate));
        $endDate = date('Y-m-d', strtotime($newsToDate));
        if(($currentDate >= $startDate) && ($currentDate <= $endDate))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getSpecialAttribute($product)
    {
        try {   
            $preSelectedValues = $product->getAttributeText('special_designations');

            if(is_array($preSelectedValues)){
                $selectedValues = $preSelectedValues;
            }else{
                $selectedValues[] = $preSelectedValues;
            }

            $finalSelectedValue = [];
            foreach ($selectedValues as $key => $specialAttrvalue) {
                if($specialAttrvalue == "Member Deals" || $specialAttrvalue == "Accessories"){
                    continue;
                }
                if($specialAttrvalue){
                    $finalSelectedValue[] = $specialAttrvalue;
                }
            }
            return $finalSelectedValue;
        } catch (\Exception $e) {

        }   
    }
    public function getClassName($label)
    {
        $label = strtolower($label);
        $label = str_replace('&', '', $label);
        $label = str_replace(' ', '-', $label);
        $label = str_replace('--', '-', $label);
        return $label;
    }
    public function getStockItem($productId)
    {
        try {
            return $this->stockRegistry->getStockItem($productId)->getQty();
        } catch(\Exception $e) {
            return 0;
        }
    }
}