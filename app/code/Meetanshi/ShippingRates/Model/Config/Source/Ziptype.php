<?php

namespace Meetanshi\ShippingRates\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Ziptype implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Numeric'),
            ],
            [
                'value' => 1,
                'label' => __('String'),
            ],
        ];
    }
}
