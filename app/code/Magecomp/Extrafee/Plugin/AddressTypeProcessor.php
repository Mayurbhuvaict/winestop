<?php
namespace Magecomp\Extrafee\Plugin;


use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class DefaultProcessor
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class AddressTypeProcessor
{
    /**
     * @var AddressType
     */
    private $addressType;

    public function __construct(
        \Magecomp\Extrafee\Model\Source\AddressType $addressType
    ){
        $this->addressType = $addressType;
    }

    /**
     * {@inheritdoc}
     */
    public function afterGetJsLayout(\Aheadworks\OneStepCheckout\Block\Checkout $subject, $jsLayout)
    {

        $jsLayout = \Zend_Json::decode($jsLayout,1);
        $customAttributeCode = 'type';

        $customField = [
            'component' => 'Magento_Ui/js/form/element/checkbox-set',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress.type',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox-set',
                /*'tooltip' => [
                    'description' => 'Addresstype',
                ],*/
            ],
            'dataScope' => 'shippingAddress.custom_attributes' . '.' . $customAttributeCode,
            'label' => 'Address Type*',
            'provider' => 'checkoutProvider',
            'sortOrder' => 200,
            'validation' => [
                'required-entry' => true
            ],
            'options' => $this->addressType->getAllOptions(),
            'filterBy' => null,
            'customEntry' => null,
            'required' => true,
            'visible' => true,
            'value' => \Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_BUSSINESS_TEXT
        ];

        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-5'] = [
            'component' => 'uiComponent',
            'config' => [
                'template' => 'Aheadworks_OneStepCheckout/form/field-row-fluid'
            ],
            'children' => [
                $customAttributeCode => $customField
            ]
        ];
        /*echo '<pre>';
        print_r($jsLayout['components']['checkout']['children']['shippingAddress']);
        die;*/

        return \Zend_Json::encode($jsLayout);
    }
}
