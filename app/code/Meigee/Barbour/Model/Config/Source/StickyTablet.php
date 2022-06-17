<?php
namespace Meigee\Barbour\Model\Config\Source;

class StickyTablet implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'stycky-tablet', 'label' => __('Enable')],
			  ['value' => '', 'label' => __('Disable')]
		];
    }
}
