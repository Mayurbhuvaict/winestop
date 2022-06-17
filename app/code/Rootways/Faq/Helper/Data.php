<?php
/**
 * FAQ Helper for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Helper;

use \Magento\Framework\View\Element\Template\Context as Templatecontext;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Templatecontext
     */
	protected $_storeManager;
    
     /**
     * @var Magento\Config\Model\ResourceModel\Config $resourceConfig
     */
	protected $resourceConfig;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Templatecontext $templatecontext
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Templatecontext $templatecontext,
		\Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->_storeManager =  $templatecontext->getStoreManager();
        $this->_customresourceConfig = $resourceConfig;
        parent::__construct($context);
    }
	
	/**
     * Get admin configuration value
     *
     * @return  string
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * Get licence key
     *
     * @return  string
     */
	public function licencekey()
	{
		return $this->getConfig('rootwaysfaq_option/general/licencekey');
	}
	
	/**
     * Get page per faq
     *
     * @return  int
     */
	public function perpagefaq()
	{
		return $this->getConfig('rootwaysfaq_option/general/perpagefaq');
	}
	
	/**
     * Get meta title
     *
     * @return  int
     */
	public function metatitle()
	{
		return $this->getConfig('rootwaysfaq_option/general/metatitle');
	}
	
	/**
     * Get meta keyword
     *
     * @return  int
     */
	public function metakeyword()
	{
		return $this->getConfig('rootwaysfaq_option/general/metakeywords');
	}
	
	/**
     * Get meta description
     *
     * @return  int
     */
	public function metadescription()
	{
		return $this->getConfig('rootwaysfaq_option/general/metadescription');
	}
    
	/**
     * Get surl
     *
     * @return  int
     */
	public function surl()
	{
        return "aHR0cHM6Ly93d3cucm9vdHdheXMuY29tL20ydmVyaWZ5bGljLnBocA==";
	}
	
	/**
     * Get store ID
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }
	
	/**
     * Get store Base URL
     *
     * @return  int
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    
	public function act()
    {
        $today = date("Y-m-d");
        $dt_db_blank = $this->getConfig('rootwaysfaq_option/general/lcstatus');
        if ($dt_db_blank == '') {
            $u = $this->_storeManager->getStore()->getBaseUrl();
            $l = $this->getConfig('rootwaysfaq_option/general/licencekey');
            $surl = base64_decode($this->surl());
            $url= $surl."?u=".$u."&l=".$l."&extname=m2_faq";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result=curl_exec($ch);
            curl_close($ch);
            $act_data = json_decode($result, true);
            if ($act_data['status'] == '0') {
                return "SXNzdWUgd2l0aCB5b3VyIFJvb3R3YXlzIGV4dGVuc2lvbiBsaWNlbnNlIGtleSwgcGxlYXNlIGNvbnRhY3QgPGEgaHJlZj0ibWFpbHRvOmhlbHBAcm9vdHdheXMuY29tIj5oZWxwQHJvb3R3YXlzLmNvbTwvYT4=";
            } else {
                $this->_customresourceConfig->saveConfig('rootwaysfaq_option/general/lcstatus', $l, 'default', 0);
            }
        }
	}
}
