<?php
namespace Meigee\CategoriesEnhanced\Model\Category\Attribute\Source;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Customlabel extends AbstractSource
{

	protected $_options;
    

    public function getAllOptions()
    {
		if (!$this->_options) {
            $this->_options = [
				['value' => '',            'label' => __('No label')],
				['value' => 'label-one',   'label' => __('Label #1')],
				['value' => 'label-two',   'label' => __('Label #2')],
				['value' => 'label-three', 'label' => __('Label #3')]
			];
		}
        return $this->_options;
    }
	
}