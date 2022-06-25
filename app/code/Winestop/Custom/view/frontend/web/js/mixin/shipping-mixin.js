define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/view/summary/discount'
], function ($, Component, quote, discountView) {
    'use strict';

    return function (target) {
        return target.extend({

            /**
             * Set shipping information handler
             */
            getShippingMethodTitle: function () {                
                var shippingMethod = '',
                shippingMethodTitle = '';

                if (!this.isCalculated()) {
                    return '';
                }
                shippingMethod = quote.shippingMethod();
               
                if (shippingMethod['carrier_title'] == 'Delivery is Free') {
                    return shippingMethod['carrier_title'];
                    //return shippingMethod;
                }

                if (typeof shippingMethod['method_title'] !== 'undefined') {
                shippingMethodTitle = ' - ' + shippingMethod['method_title'];
                }

                return shippingMethod ?
                    shippingMethod['carrier_title'] + shippingMethodTitle :
                    shippingMethod['carrier_title'];

                this._super();
            }
        });
    }
});