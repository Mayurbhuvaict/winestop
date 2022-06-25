define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'Aheadworks_OneStepCheckout/js/model/shipping-address/new-address-form-state',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'Aheadworks_OneStepCheckout/js/model/checkout-section/service-busy-flag',
    'Aheadworks_OneStepCheckout/js/model/address-list-service',
    'Magento_Customer/js/model/customer',
    'mage/url'
], function (
    $,
    ko,
    Component,
    selectShippingAddressAction,
    quote,
    checkoutData,
    customerData,
    newAddressFormState,
    completenessLogger,
    serviceBusyFlag,
    addressListSevice,
    customer,
    url
) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/shipping-address/address-renderer/default'
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            completenessLogger.bindSelectedAddressData('shippingAddress', quote.shippingAddress);
            serviceBusyFlag.subscribe(function (newValue) {
                addressListSevice.isLoading(newValue);
            }, this);
            // $(document).ajaxComplete(function() {
            //     $('.onestepcheckout-index-index .modal-inner-wrap .action-close').click(function(){
            //         if(customer.isLoggedIn()) {
            //             $('.shipping-address-item.not-selected-item').last().find('button').trigger('click');
            //         }
            //     });
            //     if(customer.isLoggedIn()) {
            //         $('.shipping-address-items li.shipping-address-item').last().find('button').trigger('click');
            //     }
            // });
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();

            this.isSelected = ko.computed(function() {
                var isSelected = false,
                    shippingAddress = quote.shippingAddress();

                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey();
                    if(this.address().regionCode && isSelected){
                        $('#custom-delivery-checker').val(this.address().regionCode).trigger('change');
                        this.restrictRegionCode(quote.shippingAddress().regionCode);
                    }

                }

                return isSelected;
            }, this);

            return this;
        },

        /**
         * Get country name
         *
         * @param {string} countryId
         * @returns {string}
         */
        getCountryName: function(countryId) {
            return (countryData()[countryId] != undefined) ? countryData()[countryId].name : '';
        },

        /**
         * On select address click event handler
         */
        onSelectAddressClick: function() {
            if (!serviceBusyFlag()) {
                selectShippingAddressAction(this.address());
                checkoutData.setSelectedShippingAddress(this.address().getKey());
                // if(this.address().regionCode){
                //     $('#custom-delivery-checker').val(this.address().regionCode).trigger('change');
                // }
            }
        },

        /**
         * On edit address click event handler
         */
        onEditAddressClick: function() {
            newAddressFormState.isShown(true);
        },
        /**
         * regioncode validator
         */
         restrictRegionCode: function(code) {
            var catname = "";
            var customurl = url.build('custom/index/Deliverycheckercontroller');
            var temp, temp_old_shipto_state, i;
            $.ajax({
                url: customurl,
                type: "post",
                data: {'code': code, 'catname': catname},
                success: function(checker) {
                    var checker = jQuery.parseJSON(checker);
                    if (checker) {
                        if (!checker.abel_to_change) {
                            window.allowRegionId = false;
                            $(".actions-toolbar span.placeorder-disallow").text("Please change your shipping address");
                        } else {
                            window.allowRegionId = true;
                            $(".actions-toolbar span.placeorder-disallow").text("");
                        }
                    }
                }
            });
        }
    });
});
