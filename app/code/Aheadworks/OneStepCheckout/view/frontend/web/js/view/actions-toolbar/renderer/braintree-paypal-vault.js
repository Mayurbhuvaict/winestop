define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        placeOrder: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().beforePlaceOrder();
            });
        }
    });
});
