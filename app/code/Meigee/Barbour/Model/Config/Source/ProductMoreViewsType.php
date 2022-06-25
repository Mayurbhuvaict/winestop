<?php
namespace Meigee\Barbour\Model\Config\Source;

class ProductMoreViewsType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'horizontal_slider', 'label' => __('Horizontal Slider')],
			  ['value' => 'list', 'label' => __('List')],
			  ['value' => 'vertical_type_1', 'label' => __('Vertical Slider Type 1')],
			  ['value' => 'vertical_type_2', 'label' => __('Vertical Slider Type 2')],
			  ['value' => 'large', 'label' => __('Large More Views')]
		];
    }
}
