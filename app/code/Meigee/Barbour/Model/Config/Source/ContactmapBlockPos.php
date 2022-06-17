<?php
namespace Meigee\Barbour\Model\Config\Source;

class ContactmapBlockPos implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
              ['value' => 'left-center', 'label' => __('Left Center')],
			  ['value' => 'left-top', 'label' => __('Left Top')],
			  ['value' => 'right-top', 'label' => __('Right Top')],
			  ['value' => 'left-bottom', 'label' => __('Left Bottom')],
			  ['value' => 'right-bottom', 'label' => __('Right Bottom')]
		];
    }
}
