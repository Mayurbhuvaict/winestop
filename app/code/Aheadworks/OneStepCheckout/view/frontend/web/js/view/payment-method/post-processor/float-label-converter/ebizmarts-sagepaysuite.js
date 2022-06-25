define(
    [
        'uiComponent',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converters-pool',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converter/sagepay-suite-pi-method'
    ],
    function (Component, convertersPool, piMethodConverter) {
        'use strict';

        convertersPool.register('sagepaysuitepi', piMethodConverter);

        return Component.extend({});
    }
);
