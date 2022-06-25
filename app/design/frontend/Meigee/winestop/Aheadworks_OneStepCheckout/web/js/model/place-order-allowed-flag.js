define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/payment/method-list',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/model/shipping-information/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/default'
    ],
    function (
        ko,
        quote,
        shippingService,
        paymentMethodList,
        sectionsServiceBusyFlag,
        shippingInfoServiceBusyFlag,
        allowPlaceOrder
    ) {
        'use strict';

        return ko.computed(function () {
            return paymentMethodList().length > 0
                && (!quote.isQuoteVirtual() && (shippingService.getShippingRates())().length > 0
                || quote.isQuoteVirtual())
                && !sectionsServiceBusyFlag()
                && !shippingInfoServiceBusyFlag() && (window.allowRegionId == true || typeof(window.allowRegionId) === "undefined");
        });
    }
);
