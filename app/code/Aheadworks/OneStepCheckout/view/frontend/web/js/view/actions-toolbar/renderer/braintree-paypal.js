define([
    'jquery',
    'ko',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, ko, Component) {
    'use strict';

    var paymentPlaceOrderBtnSelector = '.braintree-paypal-actions [data-button=paypal-place]',
        paymentActionsToolbarSelector = '.braintree-paypal-actions + .actions-toolbar';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/braintree-paypal',
            isPlaceOrderBtnVisible: false,
            isPayBtnVisible: true
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe(['isPlaceOrderBtnVisible', 'isPayBtnVisible']);

            return this;
        },

        /**
         * @inheritdoc
         */
        initMethodsRenderComponent: function () {
            this._super();

            if (this.methodRendererComponent) {
                this.isPlaceOrderBtnVisible = this.methodRendererComponent.isReviewRequired;
                this.isPayBtnVisible = ko.computed(function () {
                    return !this.methodRendererComponent.isReviewRequired();
                }, this);
                this.methodRendererComponent.isReviewRequired.subscribe(function (flag) {
                    if (flag) {
                        $(paymentPlaceOrderBtnSelector).hide();
                    }
                }, this);
            }

            if (this.isContextCheckout() && !this.isPlaceOrderBtnVisible()) {
                $.async(paymentActionsToolbarSelector, (function() {
                    $(paymentActionsToolbarSelector).show();
                }));
            }

            return this;
        },

        /**
         * Check if context checkout is enabled
         */
        isContextCheckout: function () {
            var config = window.checkoutConfig.payment[this.methodCode];

            return config && config.awOscIsContextCheckout;
        },

        /**
         * Get pay with PayPal button title
         *
         * @returns {string}
         */
        getPayBtnTitle: function () {
            return this._getMethodRenderComponent().getButtonTitle();
        },

        /**
         * Place order
         *
         * @param {Object} data
         * @param {Object} event
         */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }
            this._beforeAction().done(function () {
                self._getMethodRenderComponent().placeOrder(data, event);
            });
        },

        /**
         * Pay with PayPal
         */
        payWithPayPal: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().payWithPayPal();
            });
        }
    });
});
