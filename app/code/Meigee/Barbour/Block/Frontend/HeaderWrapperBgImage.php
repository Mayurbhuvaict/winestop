<?php
namespace Meigee\Barbour\Block\Frontend;
use \Magento\Store\Model\ScopeInterface;

class HeaderWrapperBgImage  extends \Magento\Theme\Block\Html\Header\Logo
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


    public function getHeaderWrapperBgImageSrc($cnfName = 'header_wrapper_bg_image', $cnfStatus='header_wrapper_type')
    {
        if ($this->cnf[$cnfStatus])
        {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currentStore = $storeManager->getStore();
            $base_url = $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $base_url .'/header/'. $this->cnf[$cnfName];
        }
    }



}
