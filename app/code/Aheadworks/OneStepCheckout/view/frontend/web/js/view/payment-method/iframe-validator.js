define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Aheadworks_OneStepCheckout/js/model/iframe-validator'
    ],
    function (Component, additionalValidators, iframeValidator) {
        'use strict';

        additionalValidators.registerValidator(iframeValidator);

        return Component.extend({});
    }
);
