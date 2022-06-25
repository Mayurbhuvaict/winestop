<?php

namespace Meigee\CategoriesEnhanced\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\Url\Helper\Data
{
	
	protected $scopeConfig;
	
	public function __construct(
		Context $context,
		ScopeConfigInterface $scopeConfig
	) {
		$this->scopeConfig = $scopeConfig;
		parent::__construct($context);
	}
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
				$config_path,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
				);
	}
}