var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Magecomp_Extrafee/js/action/set-shipping-information-mixin': true
            },
            'Aheadworks_OneStepCheckout/js/action/set-shipping-information': {
                'Magecomp_Extrafee/js/action/set-shipping-information-mixin': true
            }
        }
    }
};