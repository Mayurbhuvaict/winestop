<?php
namespace Meigee\ProductWidget\Model\Config\Source;
 
class Cache implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 0, 'label' => 0]
		];
    }
}