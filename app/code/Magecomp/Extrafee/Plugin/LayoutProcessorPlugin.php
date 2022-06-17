<?php

namespace Magecomp\Extrafee\Plugin;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Model\MethodFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ProductFactory;
use Meetanshi\ShippingRates\Helper\Data;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Model\AbstractModel;

class LayoutProcessorPlugin 
{
    protected $addressType;

    public function __construct(
        \Magecomp\Extrafee\Model\Source\AddressType $addressType
    ){
        $this->addressType = $addressType;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $customAttributeCode = 'type';

        $customField = [
            'component' => 'Magento_Ui/js/form/element/checkbox-set',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress.type',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox-set'
            ],
            'dataScope' => 'shippingAddress.custom_attributes' . '.' . $customAttributeCode,
            'label' => 'Address Type',
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
            'value' => '' // value field is used to set a default value of the attribute
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = $customField;
        
        return $jsLayout;
    }
}
