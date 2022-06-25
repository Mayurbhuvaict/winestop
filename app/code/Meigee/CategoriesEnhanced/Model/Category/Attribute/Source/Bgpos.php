<?php
namespace Meigee\CategoriesEnhanced\Model\Category\Attribute\Source;

class Bgpos extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

	protected $_options;
    

    public function getAllOptions()
    {
		if (!$this->_options) {
            $this->_options = [
				['value' => '1', 'label' => __('Left')],
				['value' => '2', 'label' => __('Right')],
				['value' => '3', 'label' => __('Center')],
				['value' => '4', 'label' => __('Fill with stretching')]
			];
		}
        return $this->_options;
    }
	
}