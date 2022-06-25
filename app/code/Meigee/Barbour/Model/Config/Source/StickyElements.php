<?php
namespace Meigee\Barbour\Model\Config\Source;

class StickyElements implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'sticky-logo', 'label' => __('Logo')],
			  ['value' => 'sticky-nav', 'label' => __('Navigation Menu')],
			  ['value' => 'sticky-cart', 'label' => __('Cart')],
			  ['value' => 'sticky-search', 'label' => __('Search box')]
		];
    }
}
