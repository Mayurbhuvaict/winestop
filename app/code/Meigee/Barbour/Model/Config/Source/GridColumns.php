<?php
namespace Meigee\Barbour\Model\Config\Source;

class GridColumns implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'two-columns', 'label' => __('2')],
			  ['value' => 'three-columns', 'label' => __('3')],
			  ['value' => 'four-columns', 'label' => __('4')],
			  ['value' => 'five-columns', 'label' => __('5')],
			  ['value' => 'six-columns', 'label' => __('6')],
			  ['value' => 'seven-columns', 'label' => __('7')],
			  ['value' => 'eight-columns', 'label' => __('8')]
		];
    }
}
