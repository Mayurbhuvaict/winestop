define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/model/place-order-allowed-flag',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator',
    'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data',
    'Aheadworks_OneStepCheckout/js/view/billing-address',
    'Magento_Customer/js/model/customer'
], function (
    $,
    Component,
    registry,
    fullScreenLoader,
    placeOrderAllowedFlag,
    aggregateValidator,
    aggregateCheckoutData,
    billing,
    customer
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/default',
            methodCode: null
        },
        methodRendererComponent: null,
        isPlaceOrderActionAllowed: placeOrderAllowedFlag,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super().initMethodsRenderComponent();

            return this;
        },

        /**
         * Perform before actions: overall validation, set checkout data and etc.
         *
         * @returns {Deferred}
         */
        _beforeAction: function () {
            var deferred = $.Deferred();

            if (this.isPlaceOrderActionAllowed()) {
                aggregateValidator.validate().done(function () {
                    fullScreenLoader.startLoader();
                    aggregateCheckoutData.setCheckoutData().done(function () {
                        fullScreenLoader.stopLoader();
                        deferred.resolve();
                    });
                });
            }

            return deferred;
        },

        /**
         * Init method renderer component
         *
         * @returns {Component}
         */
        initMethodsRenderComponent: function () {
            if (this.methodCode) {
                this.methodRendererComponent = registry.get('checkout.paymentMethod.methodList.' + this.methodCode);
            }

            return this;
        },

        /**
         * Get method renderer component
         *
         * @returns {Component}
         */
        _getMethodRenderComponent: function () {
            if (!this.methodRendererComponent) {
                this.initMethodsRenderComponent();
            }
            return this.methodRendererComponent;
        },

        /**
         * Place order
         *
         * @param {Object} data
         * @param {Object} event
         */
        placeOrder: function (data, event) {
            
            //$('.onestep-billing-address .action-toolbar button.action.primary').trigger('click');
            if(customer.isLoggedIn()){
                if($('.shipping-address-items').length){
                    if(!$('.shipping-address-items .shipping-address-item.selected-item').length) {
                        if(!$('.shipping-address-items .message.notice').length) {
                            $('.shipping-address-items').prepend('<div class="message notice"></div>');
                        }
                        if(!$('.shipping-address-items .error-shipping').length){
                            $('.shipping-address-items .message.notice').append('<span class="error-billing">Please select shipping address</span>');
                        }
                        return false;
                    }else{
                        $('.shipping-address-items .message.notice').remove();
                    }
                }else{
                    if(!this.isValidateCity()){
                        return false;
                    }
                }
                if(jQuery('.select').is(":visible")) {
                    jQuery('#updater').trigger('click');
                    // $('#billing-address-same-as-shipping').trigger('click');
                    // jQuery('.action-edit-address').trigger('click');
                }
            }else{
                jQuery('#updater').trigger('click');
                if(!this.isValidateCity()){
                    return false;
                }
            }
            
            if($('.onestep-billing-address .action.primary').length) {
                if(!$('#co-payment-form .message.notice').length) {
                    $('#co-payment-form').prepend('<div class="message notice"></div>');
                }
                if(!$('#co-payment-form .error-billing').length){
                    $('#co-payment-form .message.notice').append('<span class="error-billing">Please update billing address</span>');
                }else{
                    $('#co-payment-form .error-billing').html('Please update billing address');
                }
                return false;
            }else{
                $('#co-payment-form .error-billing').remove();
                if(!$('#co-payment-form .message.notice').html()){
                    $('#co-payment-form .message.notice').remove();
                }
            }
            

            var self = this;

            if (event) {
                event.preventDefault();
            }
            $('#co-payment-form .payment-method._active button.action.primary').trigger('click',function(){
                self._beforeAction().done(function () {
                    /*if($('.payment-method._active [name="payment\[method\]"').val()=='squareup_payment'){
                    }else{*/
                        self._getMethodRenderComponent().placeOrder(data, event);
                    //}
                });
            });
            
        },
        isValidateCity: function(){
            if(localStorage.getItem('deliveryMethod')=='local'){
                var localDeliveryCityNames = ['burlingame', 'hillsborough', 'san mateo', 'millbrae', 'belmont', 'san carlos', 'san bruno'];
                var city = jQuery(".onestep-shipping-address form input[name=city]");
                var enteredCity = city.val().toLowerCase();
                if (jQuery.inArray(enteredCity, localDeliveryCityNames) !== -1) {
                    window.allowRegionId = true;
                    $(".actions-toolbar span.placeorder-disallow").text("");
                    return true;
                } else {
                    window.allowRegionId = false;
                    $(".actions-toolbar span.placeorder-disallow").text("Please enter valid city");
                    return false;
                }
            }
            return true;
        },

        /**
         * Dispose subscriptions
         */
        disposeSubscriptions: function () {
        }
    });
});
