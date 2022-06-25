<?php
namespace Meigee\Barbour\Model\Config\Source;

class ImageHoverType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 0, 'label' => __('Simple')],
			  ['value' => 1, 'label' => __('Scale')]
		];
    }
}
