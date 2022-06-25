<?php
namespace Meigee\CategoriesEnhanced\Model\Category\Attribute\Source;

class Menutype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

	protected $_options;
    

    public function getAllOptions()
    {
		if (!$this->_options) {
            $this->_options = [
				['value' => '0', 'label' => __('Wide Submenu')],
				['value' => '1', 'label' => __('Default Dropdown')],
				['value' => '2', 'label' => __('Fixed')],
				['value' => '3', 'label' => __('Vertical Tabs Wide Submenu')],
				['value' => '4', 'label' => __('Horizontal Tabs Wide Submenu')]
			];
		}
        return $this->_options;
    }
	
}