<?php
namespace Meigee\Barbour\Model\Config\Source;

class TimerListType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => '0', 'label' => __('Hours: Minutes: Seconds')],
			  ['value' => '1', 'label' => __('Days: Hours: Minutes: Seconds')]
		];
    }
}
