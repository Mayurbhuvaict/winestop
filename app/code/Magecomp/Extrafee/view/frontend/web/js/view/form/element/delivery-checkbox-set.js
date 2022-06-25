define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/checkbox-set',
    'Aheadworks_OneStepCheckout/js/view/billing-address',
    'Magento_Checkout/js/view/shipping',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Aheadworks_OneStepCheckout/js/view/billing-address',
    'Magento_Customer/js/model/customer',
    'uiRegistry',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function (
        $,
        ko,
        _,
        Component,
        billing,
        shipping,
        createShippingAddress,
        selectShippingAddress,
        checkoutData,
        billingAddress,
        customer,
        uiRegistry,
        url,
        quote,
        $t,
        rateReg
    ) {
    'use strict';
    
    return Component.extend({
        /**
         * @inheritdoc
         */
        setInitialValue: function () {
            this._super();
            var self=this;
            setTimeout(function(){
                if(localStorage && localStorage.getItem('deliveryMethod')){
                    self.value(localStorage.getItem('deliveryMethod'));
                }
                self.changeValue();
            },2500);
            // var daddress = quote.billingAddress();
            var address = quote.shippingAddress();
            console.log(address);
            
            // if(customer.isLoggedIn()) {
            //     if(address.regionCode){
            //         $('#custom-delivery-checker').val(address.regionCode).trigger('change');
            //     }
            // }
            return this;
        },

        changeValue: function (event) {
            var self = this;
            var value = this.value();
            var shippingList = $('.onestep-shipping-method-list');
            var storePickup = $('#s_method_storepickup_storepickup');
            shippingList.find('.storepickup').hide();
            var quote_id = quote.getQuoteId();
            var delivery_method_url = url.build('extrafee/deliverymethod/index');
            var address = quote.shippingAddress();
            console.log(value);
            $.ajax({
                url: delivery_method_url,
                type: 'POST',
                dataType: 'json',
                async:false,
                data: {
                    'delivery_method': value,
                    'quote_id': quote_id
                },
            complete: function(response) {             
                
            },
            error: function (xhr, status, errorThrown) {
                console.log('Error happens. Try again.');
            }
            });
            if(customer.isLoggedIn()) {
                if(localStorage.getItem('deliveryMethod') && localStorage.getItem('deliveryMethod') != this.value()){
                    localStorage.removeItem('deliveryMethod');
                    localStorage.setItem('deliveryMethod', value);
                    location.reload();
                    return;
                }
            }
            localStorage.removeItem('deliveryMethod');
            if(value=='storepickup'){
                window.allowRegionId = true;
                $(".actions-toolbar span.placeorder-disallow").text("");
                localStorage.setItem('deliveryMethod',value);
                //storePickup.trigger('click');
                $('.onestep-shipping-address').parents('li').hide();
                $('.checkout-container li.shipping-method').hide();
                $(document).ajaxComplete(function() {
                    if (document.getElementById('ld_allow_cities')) {
                        $('.onestepcheckout-index-index .onestep-shipping-address #ld_allow_cities').remove();
                    }
                });
                if(!$('#billing-address-same-as-shipping').length){
                    var timeInt = setInterval(function(){
                        if($('#billing-address-same-as-shipping').length){
                            rateReg.set(address.getKey(), null);
                            rateReg.set(address.getCacheKey(), null);
                            quote.shippingAddress(address);
                            self.triggerSameAsBilling();
                            self.selectDeliveryAddress();
                            //self.selectShippingMethod(true);
                            $('#billing-address-same-as-shipping').parents('.field.choice').hide();
                            $('.aw-onestep-groups_item.payment-methods').addClass('stepone');
                            clearInterval(timeInt);
                        }
                    }, 1000);
                } else{
                    rateReg.set(address.getKey(), null);
                    rateReg.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                    //self.selectShippingMethod(true);
                    self.triggerSameAsBilling();
                    self.selectDeliveryAddress();
                }
                var sameAsLabel = $('#billing-address-same-as-shipping').parent().find('label span');
                $('#billing-address-same-as-shipping').parents('.field.choice').hide();
                if(sameAsLabel.length){
                    sameAsLabel.html($t('Billing Address'));
                }
                $('.aw-onestep-groups_item.payment-methods').addClass('stepone');
            }else{
                $('#billing-address-same-as-shipping').parents('.field.choice').show();
                $('.aw-onestep-groups_item.payment-methods').removeClass('stepone');
            }
            if(value=='local') {
                if (customer.isLoggedIn()){
                    var checkboxaddress = $('.billing-address-details');
                    var billingcheckbox = $('#billing-address-same-as-shipping');
                    if(checkboxaddress.children().length == 0) {
                        if(billingcheckbox.is(":checked") == false){
                            console.log("Checkbox is not checked.");
                            $('#billing-address-same-as-shipping').trigger('click'); 
                            console.log('fix');
                        }

                    } 
                }
                window.setShipAddr = false;
                $('.shipping-address-items li.shipping-address-item').first().find('button').trigger('click');
                localStorage.setItem('deliveryMethod',value);
                if(!$('#billing-address-same-as-shipping').length){
                    var timeIntval = setInterval(function(){
                        if($('#billing-address-same-as-shipping').length){
                            
                            rateReg.set(address.getKey(), null);
                            rateReg.set(address.getCacheKey(), null);
                            quote.shippingAddress(address);
                            
                            self.triggerSameAsBilling();
                            //self.selectShippingMethod(true);
                            $('.onestep-shipping-address').parents('li').find('.group-title span:last').html($t('Delivery Address'))
                            clearInterval(timeIntval);
                            self.resetLocalState(false);
                        }
                    }, 1000);
                } else {
                    
                    rateReg.set(address.getKey(), null);
                    rateReg.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                    
                    //self.selectShippingMethod(true);
                    self.triggerSameAsBilling();
                    self.resetLocalState(false);
                    $('.onestep-shipping-address').parents('li').find('.group-title span:last').html($t('Delivery Address'))
                }
                $('.onestep-shipping-address').parents('li').show();
                $('.checkout-container li.shipping-method').show();
                $(document).ajaxComplete(function() {  
                    if (!document.getElementById('ld_allow_cities')) {
                        $(".onestepcheckout-index-index .onestep-shipping-address form input[name='city']").after("<p id='ld_allow_cities'>Burlingame, Hillsborough, San Mateo, Millbrae, Belmont, San Carlos & San Bruno</p>");
                    }
                });
                if (!customer.isLoggedIn()) {
                    $('.onestep-shipping-address form.form').find("input[type=text], textarea, input[type=tel]").val("");
                    jQuery(".onestep-shipping-address form [name=region_id]").prop("selectedIndex", 1).change();
                } else {
                    var regionId = quote.shippingAddress().regionCode;
                    console.log('hello');
                    var city = quote.shippingAddress().city;
                    // console.log(city);
                    if (regionId) {
                        this.restrictRegionCode(regionId, city);
                    }
                    // var checkboxaddress = $('.billing-address-details');
                    // if(checkboxaddress.children().length == 0) {
                    //     $('#billing-address-same-as-shipping').trigger('click'); 
                    // } 
                }

                
                var sameAsLabel = $('#billing-address-same-as-shipping').parent().find('label span');
                if(sameAsLabel.length){
                    sameAsLabel.html($t('My billing and shipping address are the same'));
                }
                var localDeliveryCityNames = ['burlingame', 'hillsborough', 'san mateo', 'millbrae', 'belmont', 'san carlos', 'san bruno'];
                console.log(localDeliveryCityNames);
                jQuery(".onestep-shipping-address form input[name=city]").keyup(function(){
                    var enteredCity = jQuery(this).val().toLowerCase();
                    console.log(enteredCity);
                    if (jQuery.inArray(enteredCity, localDeliveryCityNames) !== -1) {
                        window.allowRegionId = true;
                        $(".actions-toolbar span.placeorder-disallow").text("");
                    } else {
                        window.allowRegionId = false;
                        $(".actions-toolbar span.placeorder-disallow").text("Please enter valid city");
                    }
                });
            }else{
                $('.onestep-shipping-address').parents('li').find('.group-title span:last').html($t('Shipping Address'));
                this.resetLocalState(true);
            }
            if(value=='ship'){
                window.setShipAddr = false;
                localStorage.setItem('deliveryMethod',value);
                $('.shipping-address-items li.shipping-address-item').first().find('button').trigger('click');
                if(!$('#billing-address-same-as-shipping').length){
                    var timeIntval = setInterval(function(){
                        if($('#billing-address-same-as-shipping').length){
                            
                            rateReg.set(address.getKey(), null);
                            rateReg.set(address.getCacheKey(), null);
                            quote.shippingAddress(address);
                            self.triggerSameAsBilling();
                            
                            //self.selectShippingMethod(false);
                            clearInterval(timeIntval);
                        }
                    }, 1000);
                }else{
                    
                    rateReg.set(address.getKey(), null);
                    rateReg.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                    
                    console.log('12345');          
                    self.triggerSameAsBilling();
                    //self.selectShippingMethod(false);
                }
                $('.onestep-shipping-address').parents('li').show();
                $('.checkout-container li.shipping-method').show();
                $(document).ajaxComplete(function() {    
                    if (document.getElementById('ld_allow_cities')) {
                        $('.onestepcheckout-index-index .onestep-shipping-address #ld_allow_cities').remove();
                    }
                });
                if (!customer.isLoggedIn()) {
                    jQuery('.onestep-shipping-address form.form').find("input[type=text], textarea, input[type=tel]").val("");
                    jQuery(".onestep-shipping-address form [name=region_id]").prop("selectedIndex", 5).change();
                }
                var sameAsLabel = $('#billing-address-same-as-shipping').parent().find('label span');
                if(sameAsLabel.length){
                    sameAsLabel.html($t('My billing and shipping address are the same'));
                }
            }
            
        },
        triggerNotSameAsBilling : function () {
            if($("#billing-address-same-as-shipping").is(":checked")){
                $('#billing-address-same-as-shipping').trigger('click');
                $('.billing-address-details button').trigger('click');
            }else{
                $('.billing-address-details button').trigger('click');
            }
        },
        triggerSameAsBilling : function () {
            if($("#billing-address-same-as-shipping").is(":checked")){
                $('#billing-address-same-as-shipping').trigger('click');
            }
        },
        selectDeliveryAddress: function () {
            var address = window.checkoutConfig.delivery_address;
            if(customer && customer.isLoggedIn()){
                address.extension_attribute={'type':'residential'};
                console.log(address);
                /*address.email=customer.customerData.email;
                address.customerId=customer.customerData.id;*/
                var newShippingAddress = createShippingAddress(address);
                newShippingAddress.save_in_address_book = 0;
                console.log(newShippingAddress);
                selectShippingAddress(newShippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress(newShippingAddress);
            }
            var addressArr = ['firstname','lastname','company','city','region','region_id','postcode','country_id','telephone'];
            addressArr.forEach(function(k,v){
                if(address.hasOwnProperty(k) && address[k]){
                    var _input = uiRegistry.get('inputName = '+k);
                    if(_input) {
                        setTimeout(function(){
                            _input.value(address[k]);
                        },500);
                    }
                }
            });
            /*if(address.hasOwnProperty('firstname') && address.firstname){
                uiRegistry.get('inputName = firstname').value(address.firstname);
            }
            if(address.hasOwnProperty('lastname') && address.lastname){
                uiRegistry.get('inputName = lastname').value(address.lastname);
            }
            if(address.hasOwnProperty('company') && address.company){
                uiRegistry.get('inputName = company').value(address.company);
            }*/
            if(address.hasOwnProperty('street')){
                if(address.street[0]){
                    uiRegistry.get('inputName = street[0]').value(address.street[0]);
                }
                if(address.street[1]){
                    uiRegistry.get('inputName = street[1]').value(address.street[1]);
                }
            }
            /*if(address.hasOwnProperty('city') && address.city){
                uiRegistry.get('inputName = city').value(address.city);
            }
            if(address.hasOwnProperty('region_id') && address.region_id){
                uiRegistry.get('inputName = region_id').value(address.region_id);
            }
            if(address.hasOwnProperty('region') && address.region){
                uiRegistry.get('inputName = region').value(address.region);
            }
            if(address.hasOwnProperty('postcode') && address.postcode){
                uiRegistry.get('inputName = postcode').value(address.postcode);
            }
            if(address.hasOwnProperty('country_id') && address.country_id){
                uiRegistry.get('inputName = country_id').value(address.country_id);
            }
            if(address.hasOwnProperty('telephone') && address.telephone){
                uiRegistry.get('inputName = telephone').value(address.telephone);
            }*/
        },
        selectShippingMethod: function (selectPickup) {
            $('.onestep-shipping-method-list .storepickup').hide();
            $('.onestep-shipping-method-list input[type=radio]').each(function(k,v){
                if(selectPickup && v.id=='s_method_storepickup_storepickup'){
                    $(v).trigger('click');
                    return false;
                }
                if(!selectPickup && v.id!='s_method_storepickup_storepickup'){
                    $(v).trigger('click');
                    return false;
                }
            })
        },
        /**
         * regioncode validator
         */
         restrictRegionCode: function(code, city) {
            var localDeliveryCityNames = ['burlingame', 'hillsborough', 'san mateo', 'millbrae', 'belmont', 'san carlos', 'san bruno'];
            var enteredCity = city.toLowerCase();
            if (jQuery.inArray(enteredCity, localDeliveryCityNames) == -1) {
                window.allowRegionId = false;
                $(".actions-toolbar span.placeorder-disallow").text("Please enter valid city");
            } else {
                var catname = "";
                var customurl = url.build('custom/index/Deliverycheckercontroller');
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
        },
        resetLocalState: function(reset){
            var region = uiRegistry.get('index = region_id');
            if(region && !customer.isLoggedIn()){
                if(reset){
                    region.setOptions(region.initialOptions);
                }else{
                    var source = region.initialOptions,
                        result;
                    result = _.filter(source, function (item) {
                        return item.title=="California" || item.value === '';
                    });
                    region.setOptions(result);
                }
            }
        }
    });
});
