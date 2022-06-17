<?php
namespace Meigee\ProductWidget\Model\Config\Source;
 
class WidgetId implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		$id = rand(0, 9999);
		return [
			  ['value' => $id, 'label' => $id]
		];
    }
}