<?php

namespace Meigee\Barbour\Model;
use \Magento\Framework\App\Config\ScopeConfigInterface;


class ViewCheck
{
    private $_scopeConfig;

    function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    function getCnfValue($path)
    {
        return $this->_scopeConfig->getValue( $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }

    function check($element)
    {
        $returnVal = null;
        if ($element->getAttribute('meigee-config-bool'))
        {
            $val = $this->getCnfValue($element->getAttribute('meigee-config-bool'));
            if (!$val)
            {
                return false;
            }
        }
        if ($element->getAttribute('meigee-config-ibool'))
        {
            $val = $this->getCnfValue($element->getAttribute('meigee-config-ibool'));
            if ($val)
            {
                return false;
            }
        }

        if ($element->getAttribute('meigee-config-template'))
        {
            $val = $this->getCnfValue($element->getAttribute('meigee-config-template'));
            if (!$val)
            {
                return false;
            }
            $element->setAttribute('template', $val);
        }


        if ($element->getAttribute('meigee-config-add-attribute'))
        {
			$allAttr = explode(':::', $element->getAttribute('meigee-config-add-attribute'));
			foreach ($allAttr as $attribute)
			{
				$attrArr = explode('|', $attribute);
				if (count($attrArr) == 2)
				{
					$val = $this->getCnfValue($attrArr[1]);
					if ($val)
					{
						$attributeCh = $element->addChild('attribute');
						$attributeCh->setAttribute('name', $attrArr[0]);
						$attributeCh->setAttribute('value', $val);
					}
				}
			}
        }

        if ($element->getAttribute('meigee-config-add-argument'))
        {
            $attrArr = explode('|', $element->getAttribute('meigee-config-add-argument'));
			if (count($attrArr) == 2)
            {
                $val = $this->getCnfValue($attrArr[1]);
                if ($val)
                {
					for ($i=0, $c=$element->arguments->argument->count(); $i<$c; $i++)
					{
						if ($attrArr[0] == $element->arguments->argument[$i]->getAttribute('name'))
						{
							$element->arguments->argument[$i] = $val;
						}
					}
                }
            }
        }
        return $returnVal;
    }
}
