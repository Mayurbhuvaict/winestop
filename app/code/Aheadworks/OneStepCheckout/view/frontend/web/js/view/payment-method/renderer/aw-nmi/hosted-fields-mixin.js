define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        return function (renderer) {
            return renderer.extend({

                onActiveChange: function () {
                    var self = this,
                        intervalId,
                        placeOrderSelector = '.aw-onestep-sidebar-content .actions-toolbar .action.checkout',
                        newPlaceOrderId = 'aw-osc-nmi-pay-button';

                    this.paymentSelector = '#' + newPlaceOrderId;
                    if (!this.active() || !this.renderer) {
                        $(placeOrderSelector).removeAttr('id');
                        return;
                    }

                    intervalId = setInterval(function () {
                        if ($(placeOrderSelector).length) {
                            clearInterval(intervalId);
                            setTimeout(function () {
                                $(placeOrderSelector).attr('id', newPlaceOrderId);
                                self.initNmi();
                            }, 1000);
                        }
                    }, 500);
                },
            });
        }
    }
);
