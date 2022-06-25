<?php
namespace Meigee\Barbour\Model\Config\Source;

class LabelsType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'label-type-1', 'label' => __('Type 1'), 'img' => 'Meigee_Barbour::images/label_type_1.png'],
			  ['value' => 'label-type-2', 'label' => __('Type 2'), 'img' => 'Meigee_Barbour::images/label_type_2.png'],
			  ['value' => 'label-type-3', 'label' => __('Type 3'), 'img' => 'Meigee_Barbour::images/label_type_3.png'],
			  ['value' => 'label-type-4', 'label' => __('Type 4'), 'img' => 'Meigee_Barbour::images/label_type_4.png']
		];
    }
}
