<?php
namespace Meigee\Barbour\Block\Frontend;

class BgSlider  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var mixed
     */
    protected $_sliderConfig;


    /**
     * BgSlider constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        $this->_sliderConfig = $this->_scopeConfig->getValue('barbour_bg_slider/barbour_bgslider_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }

    /**
     * @return bool|string
     */
    function getSlides()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currentStore = $storeManager->getStore();
        $status = (bool)$this->_sliderConfig['bgslider_status'];
        $slider = $this->_sliderConfig['bgslider_slides'];
        if ($status && !empty($slider))
        {
            $slider =  unserialize($slider);
            $base_url = $url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            $html_arr = array();
            foreach ($slider AS $slide) {
                $html_arr[] = '"'.$base_url . $slide . '"';
            }
            return implode(',', $html_arr);
        }
        return false;
    }

    /**
     * @return mixed
     */
    function getFade()
    {
        return $this->_sliderConfig['bgslider_fade'];
    }

    /**
     * @return mixed
     */
    function getDuration()
    {
        return $this->_sliderConfig['bgslider_duration'];
    }

}
