<?php
namespace Meigee\Barbour\Helper;
use Magento\Framework\App\Helper\Context;

abstract class Section  extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $configs = array();
    private $_meigeeRequest;

	function __construct(Context $context, \Magento\Framework\App\Request\Http $request) {
		$this->_meigeeRequest = $request;
		parent::__construct($context);
	}

    public function getCustomThemeOptionCnf($type, $configName)
    {
        if (!isset($this->configs[$type]))
        {
            $this->configs[$type] = $this->scopeConfig->getValue(
                $this->getThemeCnfBlock() . "/" . $type,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if (!$this->configs[$type])
            {
                $this->configs[$type] = array();
            }
        }
        return isset($this->configs[$type][$configName]) ? $this->configs[$type][$configName] : null;
    }

	function getMeigeeRequest() {
		return $this->_meigeeRequest;
	}
    abstract protected function getThemeCnfBlock();
}
