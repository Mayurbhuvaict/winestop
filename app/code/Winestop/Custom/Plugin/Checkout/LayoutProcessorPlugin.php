<?php

namespace Winestop\Custom\Plugin\Checkout;


class LayoutProcessorPlugin
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
     array  $jsLayout
    ) {
     $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['notice'] = __('Burlingame, Hillsborough, San Mateo, Millbrae, Belmont, San Carlos & San Bruno These are not allowed');
     return $jsLayout;
    }
}
