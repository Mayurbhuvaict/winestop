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
    'mage/url',
    'Magento_Ui/js/modal/modal'
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
    url,
    modal
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
                    if (isSelected) {
                        this.restrictRegionCodePopup(quote.shippingAddress().regionCode);
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
                if(this.address() ){
                    if(this.address().regionCode){
                        //$('#custom-delivery-checker').val(this.address().regionCode).trigger('change');
                        //this.restrictRegionCode(this.address().regionCode);
                    }
                }
                selectShippingAddressAction(this.address());
                checkoutData.setSelectedShippingAddress(this.address().getKey());
            }
        },

        /**
         * On edit address click event handler
         */
        onEditAddressClick: function() {
            newAddressFormState.isShown(true);
        },

        /**
         * Check regioncode and show popup
         */
        restrictRegionCodePopup: function(code) {      
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
                        $("#custom-delivery-message" ).text(checker.message);
                        if (!checker.abel_to_change) {
                            var items = checker.item;
                            var html;
                            var headline = checker.item_count + ' Item in your cart cannot be shipped to ' + checker.region_name;
                            $("span.headline" ).text(headline);
                            var old_shipto = sessionStorage.getItem("old_shipto");
                            temp = old_shipto;
                            var old_shipto_state = sessionStorage.getItem("old_shipto_state");
                            temp_old_shipto_state = old_shipto_state
                            var cancel_link = "Cancel (Stay in " + old_shipto + " )";
                            $("a.poup_cancel_state" ).text(cancel_link);
                            var header_line = '<span>Selected address has not been able to ship</span>';
                            $("div.cart_item_header" ).replaceWith(header_line);
                            var product_ids;
                            for (i = 0; i < items.length; i++) {
                                var item = items[i];
                                if (html) {
                                    html = html + '<div class="cart_item"><div class="item_image_content"><img src="' + item.image_url +'"></div><div class="ship_item_content"><span class="item-name">' + item.name +'</span><span class="item_price">' + item.price +'</span></div></div>';
                                    product_ids = product_ids + "," + item.item_id;
                                } else {
                                    html = '<div class="cart_item"><div class="item_image_content"><img src="' + item.image_url +'"></div><div class="ship_item_content"><span class="item-name">' + item.name +'</span><span class="item_price">' + item.price +'</span></div></div>';
                                    product_ids = item.item_id;
                                }
                            }
                            $("div.items_container").replaceWith(html);
                            var options = {
                                type: 'popup',
                                responsive: true,
                                innerScroll: true,
                                modalClass: 'ship_to_popup_model',
                                buttons: [{
                                    text: $.mage.__('Continue (ship to ' + code + ' )'),
                                    class: '',
                                    click: function () {
                                        var product_ids = sessionStorage.getItem("product_ids");
                                        var customurl = "<?php echo $this->getUrl().'custom/index/Remove'?>";
                                        $.ajax({
                                            url: customurl,
                                            type: "post",
                                            data: {'product_ids': product_ids},
                                            success: function(checker) {
                                                var checker = jQuery.parseJSON(checker);
                                                $('.sprite_custom').parent().css('display','none');
                                                if (checker) {
                                                    if (checker.success) {
                                                         window.location.href='/';
                                                         sessionStorage.setItem("shipto", code);
                                                    }
                                                }
                                            }
                                        });
                                        this.closeModal();
                                    }
                                }]
                            };
                            var popup = modal(options, $('#popup-modal'));
                            $('#popup-modal').modal('openModal');
                            sessionStorage.setItem("product_ids", product_ids);
                            checker.status = false;
                        }
                        sessionStorage.setItem("shipto", code);
                        sessionStorage.setItem("shipto_state", checker.regionId);
                        if (checker.status) {
                            $('.custom_ship_to').css('display','none');
                            $('.sidebar.col-sm-3.left-sidebar').css('display','none');
                            $('.ship_to_message').css('display','block');
                        } else {
                            $('.ship_to_message').css('display','none');
                            $('.sidebar.col-sm-3.left-sidebar').css('display','block');
                            $('.custom_ship_to').css('display','block');
                        }
                    }
                }
            });
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
                            console.log("not able");
                        } else {
                            console.log("able");
                        }
                    }
                }
            });
        }
    });
});
