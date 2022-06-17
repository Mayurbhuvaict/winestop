<?php
namespace Winestop\Custom\Plugin;

class Config
{
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $options['latest'] = __("Newest Products");
        $options['bestseller'] = __("Best Selling");
        // $options['low_to_high'] = __('Price - Low To High');
        // $options['high_to_low'] = __('Price - High To Low');
        array_shift($options);

        $test = [];
        $test['latest'] = $options['latest'];
        $test['name'] = $options['name'];
        $test['bestseller'] = $options['bestseller'];
        $test['price'] = $options['price'];
        $test['vintage'] = $options['vintage'];
        return $test;
       
    }

}