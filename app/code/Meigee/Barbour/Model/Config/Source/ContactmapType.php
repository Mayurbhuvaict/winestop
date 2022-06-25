<?php
namespace Meigee\Barbour\Model\Config\Source;

class ContactmapType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'ROADMAP', 'label' => __('Normal street map')],
			  ['value' => 'SATELLITE', 'label' => __('Satellite images')],
			  ['value' => 'TERRAIN', 'label' => __('Map with physical features')]
		];
    }
}
