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

    /**
     * @var DeliveryType
     */
    private $deliveryType;
    private $checkoutSession;
    private $customerSession;

    public function __construct(
        \Magecomp\Extrafee\Model\Source\AddressType $addressType,
        \Magecomp\Extrafee\Model\Source\DeliveryType $deliveryType,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->addressType = $addressType;
        $this->deliveryType = $deliveryType;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
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
            'value' => \Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_RESIDENTIAL_TEXT
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
        $quote = $this->checkoutSession->getQuote();
        $deliveryMethod = \Magecomp\Extrafee\Model\Source\DeliveryType::STOREPICKUP_CODE;
        if($quote && $quote->getDeliveryMethod()){
            $deliveryMethod = $quote->getDeliveryMethod();
        }
         $customFieldDeliveryMethod = [
            'config' => [
            'deps' => [
                    'checkoutProvider',
                    'checkout.checkoutConfig'
                ]
            ],

            'component' => 'Magecomp_Extrafee/js/view/delivery-methods',
            'displayArea' => 'deliveryMethod',
            'provider' => 'checkoutProvider',
            'sortOrder' => 6,
            'children' => [
                'delivery-method-fieldset' => [
                        'component' => 'uiComponent',
                        'config' => [
                            'deps' => [
                                'checkoutProvider'
                            ]
                        ],
                        'displayArea' => 'delivery-method-fieldset',
                        'children' =>[
                                'field-row-0' => [
                                    //'component' => 'Magento_Ui/js/form/element/checkbox-set',
                                    'component' => 'Magecomp_Extrafee/js/view/form/element/delivery-checkbox-set',
                                    'config' => [
                                        // customScope is used to group elements within a single form (e.g. they can be validated separately)
                                        'customScope' => 'deliveryMethod.type',
                                        'customEntry' => null,
                                        'template' => 'ui/form/field',
                                        'elementTmpl' => 'ui/form/element/checkbox-set',
                                    ],
                                    'dataScope' => 'deliveryMethod.deliveryMethod',
                                    'label' => '',
                                    'provider' => 'checkoutProvider',
                                    'sortOrder' => 1,
                                    'validation' => [
                                        'required-entry' => true
                                    ],
                                    'options' => $this->deliveryType->toOptionArray(),
                                    'filterBy' => null,
                                    'customEntry' => null,
                                    'required' => true,
                                    'visible' => true,
                                    'value' => $deliveryMethod,
                                    'listens'=>
                                    ['value'=> "changeValue"]
                                ]
                        ]
                    ]
                ]
        ];
        $jsLayoutDeliveryMethod['deliveryMethod'] = $customFieldDeliveryMethod;
        $before_slice = array_slice($jsLayout['components']['checkout']['children'], 0, 5);
        $after_slice = array_slice($jsLayout['components']['checkout']['children'], 5);
        $jsLayout['components']['checkout']['children'] = array_merge($before_slice, $after_slice, $jsLayoutDeliveryMethod);
        
        //shippin
        $shippingAddr = $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
        
        if(isset($shippingAddr['field-row-4']) && isset($shippingAddr['field-row-4']['children']) && isset($shippingAddr['field-row-4']['children']['region'])){
            $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-4']['children']['region']['label'] = 'State';
            $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-4']['children']['region_id']['label'] = 'State';
        }
        $billAddr = $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children'];
        if(isset($billAddr['field-row-4']) && isset($billAddr['field-row-4']['children']) && isset($billAddr['field-row-4']['children']['region'])){
            $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children']['field-row-4']['children']['region']['label'] = 'State';
            $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children']['field-row-4']['children']['region_id']['label'] = 'State';
        }
        //country_id
        if(isset($shippingAddr['included-country-field-row']) && isset($billAddr['included-country-field-row']['children']) && isset($billAddr['included-country-field-row']['children']['country_id'])){
            $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['included-country-field-row']['children']['country_id']['config']['additionalClasses']='d-none';
        }
        if(isset($billAddr['included-country-field-row']) && isset($billAddr['included-country-field-row']['children']) && isset($billAddr['included-country-field-row']['children']['country_id'])){
            $jsLayout['components']['checkout']['children']['paymentMethod']['children']['billingAddress']['children']['billing-address-fieldset']['children']['included-country-field-row']['children']['country_id']['config']['additionalClasses'] = 'd-none';
        }
        if($this->customerSession->isLoggedIn() && $quote && $quote->getDeliveryMethod()){
            $deliveryMethod = $quote->getDeliveryMethod();
            if($deliveryMethod=='local'){
                $directory = $jsLayout['components']['checkoutProvider']['dictionaries'];
                if(isset($directory['region_id'])){
                    $dictionary = [];
                    foreach ($directory['region_id'] as $key => $value) {
                        if(isset($value['label']) && ( !$value['value'] || $value['label']=='California')) {
                            $dictionary[$key]=$value;
                        }
                    }
                    $jsLayout['components']['checkoutProvider']['dictionaries']['region_id']=$dictionary;
                }
            }
        }
        return \Zend_Json::encode($jsLayout);
    }
}
