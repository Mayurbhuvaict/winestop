<?php

namespace Meigee\Barbour\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Divider extends Field
{
	
	public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
		
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $label = $element->getLabelHtml();
		
		$label = str_replace('class="', 'class="divider ', $label);
		
		$element->setLabelHtml($label);
		return $label ;
        
    }


}
