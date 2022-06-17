<?php
namespace Meigee\Barbour\Model\Config\Source;

class InputRadioSource implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
                      ['value' => 1, 'title' => __('title 1'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                    , ['value' => 2, 'title' => __('title 2'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                    , ['value' => 3, 'title' => __('title 3'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                ];
    }
}
