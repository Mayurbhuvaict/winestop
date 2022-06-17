<?php
namespace Meigee\Barbour\Model\Config\Source;

class HeaderSearchType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'type-1', 'label' => __('Type 1')],
			  ['value' => 'type-2', 'label' => __('Type 2')]
		];
    }
}
