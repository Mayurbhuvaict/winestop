define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
    ],
    function ($, quote) {
        'use strict';

        return function (renderer) {
            return renderer.extend({

                /**
                 * Set list of observable attributes
                 * @returns {exports.initObservable}
                 */
                initObservable: function () {
                    var self = this;

                    this._super();

                    quote.shippingAddress.subscribe(function () {
                        if (self.isActive()) {
                            self.reInitPayPal();
                        }
                    });

                    return this;
                },

                /**
                 * Get shipping address
                 * @returns {Object}
                 */
                getShippingAddress: function () {
                    var address = quote.shippingAddress();

                    if (_.isNull(address.postcode) || _.isUndefined(address.postcode)) {
                        return {};
                    }

                    return this._super();
                },
            });
        }
    }
);