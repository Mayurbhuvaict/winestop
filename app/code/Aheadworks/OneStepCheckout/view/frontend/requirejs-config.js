var config = {
    map: {
        '*': {
            awOscSidebar:           'Aheadworks_OneStepCheckout/js/sidebar',
            awOscFloatLabel:        'Aheadworks_OneStepCheckout/js/float-label',
            awOscAsyncAccordion:    'Aheadworks_OneStepCheckout/js/async-accordion',
            awOscValidationMock:    'Aheadworks_OneStepCheckout/js/validation-mock'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Aheadworks_OneStepCheckout/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Aheadworks_OneStepCheckout/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Aheadworks_OneStepCheckout/js/model/set-payment-information-mixin': true
            },
            'Magento_CheckoutAgreements/js/model/agreements-modal': {
                'Aheadworks_OneStepCheckout/js/model/checkout-agreements/modal-mixin': true
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Aheadworks_OneStepCheckout/js/model/checkout-agreements/assigner-mixin': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Aheadworks_OneStepCheckout/js/view/totals/abstract-total-mixin': true
            },
            'Magento_Checkout/js/model/quote': {
                'Aheadworks_OneStepCheckout/js/model/quote-mixin': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'Aheadworks_OneStepCheckout/js/view/form/element/abstract-mixin': true
            },
            'Magento_Checkout/js/action/select-billing-address': {
                'Aheadworks_OneStepCheckout/js/model/select-billing-address-mixin': true
            },
            'Magento_Braintree/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/hosted-fields-mixin': true
            },
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/paypal-mixin': true
            },
            'Magento_Checkout/js/action/get-payment-information': {
                'Aheadworks_OneStepCheckout/js/action/get-payment-information-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Aheadworks_OneStepCheckout/js/model/checkout-express-mixin': true
            },
            'Aheadworks_Nmi/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/aw-nmi/hosted-fields-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/paypal/payflowpro-method-mixin': true
            }
        }
    }
};
