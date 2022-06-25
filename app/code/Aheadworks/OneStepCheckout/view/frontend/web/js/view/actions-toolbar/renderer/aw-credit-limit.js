define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'uiRegistry'
], function (Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/aw-credit-limit',
            deps: [
                'checkout.paymentMethod.methodList.aw_credit_limit'
            ],
            isPlaceOrderButtonVisible: true
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe(['isPlaceOrderButtonVisible']);

            return this;
        },

        /**
         * @inheritdoc
         */
        initMethodsRenderComponent: function () {
            this._super();

            registry.async('checkout.paymentMethod.methodList.aw_credit_limit')(
                function (component) {
                    this.isPlaceOrderButtonVisible(component.isBalanceEnoughToPay());
                    component.isActionToolbarVisible = function () {
                        return false;
                    };
                }.bind(this)
            );

            return this;
        }
    });
});