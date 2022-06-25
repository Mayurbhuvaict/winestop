<?php
namespace Meigee\Barbour\Block\Frontend;
use \Magento\Store\Model\ScopeInterface;

class CustomLogo  extends \Magento\Theme\Block\Html\Header\Logo
{
    private $cnf;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        array $data = []
    )
    {
          parent::__construct($context, $fileStorageHelper, $data);
          $this->cnf = $this->_scopeConfig->getValue('barbour_general/barbour_logo', ScopeInterface::SCOPE_STORE);
    }


    public function getLogoSrc($cnfName = 'custom_logo_image', $cnfStatus='custom_logo_status')
    {
        if ($this->cnf[$cnfStatus])
        {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currentStore = $storeManager->getStore();
            $base_url = $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $base_url .'/logo/'. $this->cnf[$cnfName];
        }
        else
        {
            return parent::getLogoSrc();
        }
    }

    public function getLogoAlt($cnfName = 'custom_logo_alt')
    {
        if ($this->cnf['custom_logo_status'])
        {
            return $this->cnf[$cnfName];
        }
        else
        {
            return parent::getLogoAlt();
        }
    }




}
