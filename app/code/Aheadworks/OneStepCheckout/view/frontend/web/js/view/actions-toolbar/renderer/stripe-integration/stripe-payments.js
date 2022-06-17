define([
    'jquery',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data',
    'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
    'Magento_Checkout/js/action/select-billing-address'
], function (
    $,
    Component,
    customer,
    quote,
    fullScreenLoader,
    aggregateValidator,
    aggregateCheckoutData,
    sameAsShippingFlag,
    selectBillingAddressAction
) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function () {
            var self = this;

            this._super()
                .reinitBillingAddress();

            quote.shippingAddress.subscribe(function () {
                self.reinitBillingAddress();
            });
            return this;
        },

        /**
         * Re-init billing address if it's the same as shipping
         */
        reinitBillingAddress: function () {
            if (!quote.isQuoteVirtual() && sameAsShippingFlag.sameAsShipping()) {
                selectBillingAddressAction(quote.shippingAddress());
            }
            stripe.quote = quote;
        },

        /**
         * @inheritDoc
         */
        _beforeAction: function () {
            var deferred = $.Deferred();

            if (this.isPlaceOrderActionAllowed()) {
                this._createPaymentToken().done(function () {
                    aggregateValidator.validate().done(function () {
                        fullScreenLoader.startLoader();
                        aggregateCheckoutData.setCheckoutData().done(function () {
                            fullScreenLoader.stopLoader();
                            deferred.resolve();
                        });
                    });
                });
            }

            return deferred;
        },

        /**
         * Create payment token
         *
         * @private
         * @return {*}
         */
        _createPaymentToken: function () {
            var deferred = $.Deferred(),
                renderComponent = this._getMethodRenderComponent();

            renderComponent.stripePaymentsStripeJsToken(null);
            renderComponent.stripeCreatingToken(true);

            stripe.quote = quote;
            stripe.customer = customer;

            if (!renderComponent.isNewCard()) {
                stripe.sourceId = stripe.cleanToken(renderComponent.stripePaymentsSelectedCard());
            }

            createStripeToken(function(err, token, response) {
                renderComponent.stripeCreatingToken(false);
                if (err) {
                    renderComponent.showError(renderComponent.maskError(err));
                    renderComponent.resetApplePay();
                    deferred.reject();
                } else {
                    renderComponent.stripePaymentsStripeJsToken(token);
                    deferred.resolve();
                }
            });

            return deferred;
        }
    });
});
