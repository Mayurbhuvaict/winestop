<?php
namespace Meigee\Barbour\Model\Config\Source;

class ProductDescriptionCollapse implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => '1', 'label' => __('No')],
			  ['value' => '0', 'label' => __('Yes')]
		];
    }
}
