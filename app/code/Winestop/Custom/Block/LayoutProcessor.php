<?php
namespace Winestop\Custom\Block;

class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
       \Aheadworks\OneStepCheckout\Model\Layout\Processor\DefaultProcessor $subject,
       array $jsLayout
    ) {
        // Firstname
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['name-field-row']['children']['firstname']['validation']['validate-alpha'] = true;

        // Lastname
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['name-field-row']['children']['lastname']['validation']['validate-alpha'] = true;

        $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children']['name-field-row']['children']['firstname']['validation']['validate-alpha'] = true;

        $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children']['name-field-row']['children']['lastname']['validation']['validate-alpha'] = true;


        return $jsLayout;
    }
}
