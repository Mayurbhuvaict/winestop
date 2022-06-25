<?php

namespace Meigee\Barbour\Block;

class Template extends \Magento\Framework\View\Element\Template {
    public $_coreRegistry;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_logo = $logo;
        parent::__construct($context, $data);
    }
    
    public function getConfig($config_path, $storeCode = null)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }
    public function isHomePage()
    {   
        return $this->_logo->isHomePage();
    }
    
}
?>