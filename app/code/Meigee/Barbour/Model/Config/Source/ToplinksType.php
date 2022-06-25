<?php
namespace Meigee\Barbour\Model\Config\Source;

class ToplinksType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 0, 'label' => __('Custom')],
			  ['value' => 1, 'label' => __('Default')]
		];
    }
}
