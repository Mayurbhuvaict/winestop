<?php
namespace Meigee\CategoriesEnhanced\Model\Category\Attribute\Source;

class Verticaltype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

	protected $_options;
    

    public function getAllOptions()
    {
		if (!$this->_options) {
            $this->_options = [
				['value' => 'default-open', 'label' => __('Always Open')],
				['value' => 'hover', 'label' => __('Hover')]
			];
		}
        return $this->_options;
    }
	
}