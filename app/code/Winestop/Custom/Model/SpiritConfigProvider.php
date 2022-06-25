<?php 
namespace Winestop\Custom\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * 
 */
class SpiritConfigProvider implements ConfigProviderInterface
{
	/** @var LayoutInterface  */   
   
 
   public function __construct(ScopeConfigInterface $scopeConfig)
   {
             
       $this->_scopeConfig = $scopeConfig;
   } 
  
   public function getConfig()
   {
        $config = [];
       
        $config['onlyWineState'] = $this->getSpiritValue();
        return $config;
   }

   public function getSpiritValue()
   {
        $myconfig = $this->_scopeConfig->getValue('custom/general/only_wine_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $myconfig;
   }
}