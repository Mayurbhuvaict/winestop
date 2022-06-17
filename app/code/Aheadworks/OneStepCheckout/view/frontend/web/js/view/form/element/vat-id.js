define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Checkout/js/model/shipping-rates-validator'
], function (Abstract, shippingRatesValidator) {
    'use strict';

    return Abstract.extend({

        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable
         */
        initObservable: function () {
            this._super();

            shippingRatesValidator.bindHandler(this);

            return this;
        }
    });
});
