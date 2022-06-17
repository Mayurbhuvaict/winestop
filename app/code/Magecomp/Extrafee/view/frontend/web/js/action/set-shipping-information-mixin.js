/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, customer, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            window.ship = shippingAddress;
            if(shippingAddress){
                if (shippingAddress['extension_attributes'] === undefined) {
                    shippingAddress['extension_attributes'] = {};
                }
                if(shippingAddress.customAttributes && shippingAddress.customAttributes.length){
                    $(shippingAddress.customAttributes).each(function(k,v){
                        if(v.attribute_code=='type'){
                            shippingAddress['extension_attributes']['type'] = v.value;
                        }
                    });
                }
            }
            window.shipAdd = shippingAddress;
            return originalAction();
        });
    };
});
