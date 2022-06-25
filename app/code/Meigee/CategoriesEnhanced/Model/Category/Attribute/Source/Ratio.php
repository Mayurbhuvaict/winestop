<?php
namespace Meigee\CategoriesEnhanced\Model\Category\Attribute\Source;

class Ratio extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * @return array
     */
    public function getAllOptions()
    {
		if (!$this->_options) {
            $this->_options = [
				['value' => '',		'label' => ' '],
				['value' => '10/2',	'label' => '10/2'],
				['value' => '9/3',	'label' => '9/3'],
				['value' => '8/4',	'label' => '8/4'],
				['value' => '7/5',	'label' => '7/5'],
				['value' => '6/6',	'label' => '6/6'],
				['value' => '5/7',	'label' => '5/7'],
				['value' => '4/8',	'label' => '4/8'],
				['value' => '3/9',	'label' => '3/9'],
				['value' => '2/10',	'label' => '2/10']
			];
		}
        return $this->_options;
    }
}