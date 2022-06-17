<?php


namespace Meigee\Barbour\Block\Frontend;


class Currency extends \Magento\Directory\Block\Currency
{
    private $currencySymbols = array();
    private $_locale = '';

    public function __construct(
				\Magento\Framework\View\Element\Template\Context $context,
				\Magento\Directory\Model\CurrencyFactory $currencyFactory,
				\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
				\Magento\Framework\Locale\ResolverInterface $localeResolver,
				array $data = []
		)
	{
	    parent::__construct($context, $currencyFactory, $postDataHelper, $localeResolver, $data);
	    $this->_locale = str_replace('_', '-', $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE ));
	}

	function getSymbol($currency)
	{
		if (isset($this->currencySymbols[$currency]))
		{
			return $this->currencySymbols[$currency];
		}
		$fmt = new \NumberFormatter( $this->_locale."@currency=".$currency, \NumberFormatter::CURRENCY );
		$this->currencySymbols[$currency] = $fmt->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
		return $this->currencySymbols[$currency];
	}
}
