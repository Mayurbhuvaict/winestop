<?php 
namespace Winestop\Custom\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * 
 */
class ConfigProvider implements ConfigProviderInterface
{
	/** @var LayoutInterface  */
   protected $_layout;
   protected $cmsBlock;
   protected $seccmsBlock;
 
   public function __construct(LayoutInterface $layout, ScopeConfigInterface $scopeConfig, $blockId, $blockIdsec)
   {
       $this->_layout = $layout;
       $this->cmsBlock = $this->constructBlock($blockId);
       $this->seccmsBlock = $this->constructBlock($blockIdsec);
       $this->_scopeConfig = $scopeConfig;
   }
 
   public function constructBlock($blockId){
       $block = $this->_layout->createBlock('Magento\Cms\Block\Block')
           ->setBlockId($blockId)->toHtml();       
       return $block;
   }
 
   public function getConfig()
   {
        $config = [];
        $config['cms_block'] = $this->cmsBlock;
        $config['sec_block'] = $this->seccmsBlock;
        $config['glsLabel'] = $this->getGlsLabel();
        return $config;
   }

   public function getGlsLabel()
   {
        $myconfig = $this->_scopeConfig->getValue('carriers/gsoshipping/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $myconfig;
   }
}