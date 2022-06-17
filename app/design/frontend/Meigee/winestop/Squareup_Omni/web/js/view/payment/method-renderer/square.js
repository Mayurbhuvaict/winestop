define(
    [
        'jquery',
        'ko',
        'Magento_Payment/js/view/payment/cc-form',
        'mage/url',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'Squareup_Omni/js/view/payment/giftcard/square',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/step-navigator',
        'accordion',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/action/create-shipping-address',
        'Aheadworks_OneStepCheckout/js/view/billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/model/customer'
    ],
    function (
        $,
        ko,
        Component,
        url,
        additionalValidators,
        redirectOnSuccessAction,
        squareGiftCard,
        quote,
        priceUtils,
        fullScreenLoader,
        stepNavigator,
        accordion,
        selectBillingAddressAction,
        selectShippingAddressAction,
        createShippingAddress,
        billing,
        checkoutData,
        customer
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: {
                    name: 'Squareup_Omni/payment/square'
                },
                SquareupApplicationId: window.checkoutConfig.payment.squareup.squareupApplicationId,
                SquareupLocationId: window.checkoutConfig.payment.squareup.squareupLocationId,
                giftCards: squareGiftCard.giftCards,
                giftCardBalance: squareGiftCard.giftCardBalance,
                displayForm: squareGiftCard.displayForm,
                cardBrand: 'unknown',
                requestShippingAddress: false,
                requestBillingInfo: false,
                currencyCode: window.checkoutConfig.payment.squareup.squareupCurrencyCode,
                squareupMerchantName: window.checkoutConfig.payment.squareup.squareupMerchantName,
                isApplePayEnabled: ko.observable(window.checkoutConfig.payment.squareup.isApplePayEnabled),
                isGooglePayEnabled: ko.observable(window.checkoutConfig.payment.squareup.isGooglePayEnabled),
                options: {},
                postalCode: ko.observable('')
            },
            verificationToken: null,

            context: function () {
                return this;
            },

            getCode: function () {
                return 'squareup_payment';
            },

            isActive: function () {
                return true;
            },

            getActionUrl: function () {
                return url.build('square/index/saveNonce');
            },

            getHaveSavedCards: function () {
                return window.checkoutConfig.payment.squareup.getHaveSavedCards;
            },

            getIsSaveOnFileEnabled: function () {
                return window.checkoutConfig.payment.squareup.getIsSaveOnFileEnabled;
            },

            displaySaveCcCheckbox: function () {
                return window.checkoutConfig.payment.squareup.displaySaveCcCheckbox;
            },

            getCustomerCards: function () {
                if (typeof(window.checkoutConfig.payment.squareup.customerCards.length) !== "undefined"
                    && typeof(window.checkoutConfig.payment.squareup.customerCards.length) !== "number") {
                    var cards = [];

                    Object.keys(window.checkoutConfig.payment.squareup.customerCards)
                        .forEach(function (key, index) {
                            window.checkoutConfig.payment.squareup.customerCards[key]['card_id'] = key;
                            cards.push(window.checkoutConfig.payment.squareup.customerCards[key]);
                        });
                    window.checkoutConfig.payment.squareup.customerCards = cards;
                    return cards;
                } else if (typeof(window.checkoutConfig.payment.squareup.customerCards.length) === "number") {
                    return window.checkoutConfig.payment.squareup.customerCards;
                } else if (typeof(window.checkoutConfig.payment.squareup.customerCards) === "object") {
                    var cards = [];

                    Object.keys(window.checkoutConfig.payment.squareup.customerCards)
                        .forEach(function (key, index) {
                            window.checkoutConfig.payment.squareup.customerCards[key]['card_id'] = key;

                            cards.push(window.checkoutConfig.payment.squareup.customerCards[key]);
                        });
                    return cards;
                } else {
                    return [];
                }
            },

            getCardInputTitle: function (data) {
                 return data.card_brand + " | " + data.exp_month + "/" + data.exp_year + " | **** " + data.last_4;
            },

            getCanSaveCards: function () {
                return window.checkoutConfig.payment.squareup.getCanSaveCards;
            },

            onlyCardOnFileEnabled: function () {
                return window.checkoutConfig.payment.squareup.onlyCardOnFileEnabled;
            },

            isGiftCardEnabled: function () {
                return window.checkoutConfig.payment.squareup.isGiftCardEnabled;
            },

            isGiftCardUsed: function() {
                if (this.isGiftCardEnabled()) {
                    return this.giftCards().length > 0;
                }

                return false;
            },

            hasSubscriptionProducts: function() {
                return window.checkoutConfig.payment.squareup.hasSubscriptionProducts;
            },

            placeOrder: function (data, event) {
                var self = this;
                let method = jQuery("div.payment-option.credit-card._active").attr("method");
                if(method!='card'){
                    var existingNonce = document.getElementById('card-nonce');
                    if (!existingNonce.value.length) {
                        self.handleSubmit();
                    }
                }
                if (billing().isAddressSameAsShipping() && $('.onestep-shipping-address').is(':visible') && !customer.isLoggedIn()) {
                    var addressData = checkoutData.getShippingAddressFromData();
                    var add = createShippingAddress(addressData);
                    selectBillingAddressAction(add);

                    var newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddressAction(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                }
                if (event) {
                    event.preventDefault();
                }
                
                this.isPlaceOrderActionAllowed(false);
                this.getPlaceOrderDeferredObject()
                    .fail(
                        function (response) {
                            squareGiftCard.refreshGiftCards();

                            if(response.responseJSON.message.indexOf('Nonce')>-1){
                                var existingNonce = document.getElementById('card-nonce');
                                if (existingNonce.value.length === 0) {
                                    self.placeOrderClick();
                                }
                            }else{
                                alert(response.responseJSON.message);
                                self.displayForm(true);
                                document.getElementById('card-nonce').value = "";
                            }


                            self.isPlaceOrderActionAllowed(true);

                            return response;
                        }
                    ).done(
                    function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );

                return true;
            },

            initialize: function () {

                this._super();
                var _self = this;

                window.Squareup = {};

                /* Initialize options function */
                var init = function () {
                    _self.initOptions();
                };

                /* Initialize options event trigger */
                /* wait for document to be loaded to initialize the events */
                $(document).ready(function () {
                    init();
                    window.addEventListener('hashchange', _.bind(_self.onStepChange, stepNavigator));
                });
            },

            onStepChange: function() {
                squareGiftCard.refreshGiftCards();
            },

            initOptions: function () {
                this.options = {
                    // Initialize Apple Pay placeholder ID
                    applePay: {
                        elementId: 'applePay'
                    },

                    // Initialize Masterpass placeholder ID
                    masterpass: false
                };
            },

            initPayment: async function () {
                var _self = this;

                if (parseFloat(window.checkoutConfig.quoteData.grand_total) > 0) {
                    _self.displayForm(true);
                }

                window.squareupCardOnFileUsed = false;
                console.log('Initing payment');
                //add class to google pay and apple pay for logo
                $('div[method="googlePay"]').addClass('google-pay');
                $('div[method="applePay"]').addClass('apple-pay');
                await this.squareCard();
                await this.squareApplePay();
                await this.squareGooglePay();

                $(".sq-accordion").accordion({
                    "openedState": "_active"
                });
                $(".sq-accordion-gift-card").accordion({"active": [0], "openedState": "_active"});

                if (this.isGiftCardEnabled()) {
                    squareGiftCard.getGiftCards();

                    /** Build gift card form*/
                    squareGiftCard.squareGiftcard();
                    squareGiftCard.refreshGiftCards();
                }

                quote.square = _self;
                squareGiftCard.square = _self;

                $(document).ready(function () {
                    if(_self.onlyCardOnFileEnabled() == true && window.checkoutConfig.payment.squareup.getHaveSavedCards) {
                        jQuery('#save_square_cards_empty').val('0');
                        jQuery('#save-square-card').val('0');
                    } else if(_self.onlyCardOnFileEnabled() == true) {
                        jQuery('#save_square_cards_empty').val('1');
                        jQuery('#save-square-card').val('1');
                    }
                    $('body').on('change', 'input[type=radio][name=squareup_cards]', function () {
                        if (this.value == 'other_card') {
                            if (_self.onlyCardOnFileEnabled() == true) {
                                var input = document.createElement("input");
                                input.type = 'hidden';
                                input.name = 'payment[save_square_card]';
                                input.id = 'save_card_on_file_input';
                                input.value = 1;
                                document.getElementById("square_form_fields").appendChild(input);
                            }
                            document.getElementById("square_form_fields").style.display = "block";
                            jQuery('input[name="payment[nonce]"]').val('');
                            window.squareupCardOnFileUsed = false;
                            // $('#payment_form_squareup_payment + .actions-toolbar .primary').addClass('hide');
                        } else {
                            var element =  document.getElementById('save_card_on_file_input');
                            if (typeof(element) != 'undefined' && element != null) {
                                // jQuery('input[name="payment[save_square_card]"]').remove();
                                jQuery('input[name="payment[save_square_card]"]').val('');
                            }
                            document.getElementById("square_form_fields").style.display = "none";
                            jQuery('input[name="payment[nonce]"]').val(this.value);
                            window.squareupCardOnFileUsed = true;
                            // $('#payment_form_squareup_payment + .actions-toolbar .primary').removeClass('hide');
                        }
                    });
                });
            },

            getLineItems: function(totals) {
                var lineItems = [];

                totals.total_segments.forEach(function(total) {
                    if (total.code === 'grand_total') {
                        return;
                    }

                    if (total.code === 'shipping') {
                        var amount = totals.shipping_incl_tax
                    } else {
                        var amount = total.value;
                    }

                    lineItems.push({
                        label: total.title,
                        amount: Number(amount).toFixed(2).toString(),
                        pending: false
                    });
                });

                return lineItems;
            },

            getGrandTotal: function(totals) {
                var grandTotal = 0;

                totals.total_segments.forEach(function(total) {
                    if (total.code === 'grand_total') {
                        grandTotal = total.value.toString();
                    }
                });

                return grandTotal;
            },

            grandTotal: function () {
                return (this.getGrandTotal(quote.totals()) - this.giftcardTotal());
            },

            squarePaymentRequest: function() {
                var shippingAddressData = quote.shippingAddress();
                var email = (!quote.guestEmail)? window.checkoutConfig.customerData.email : quote.guestEmail;
                var grandTotal = this.grandTotal();

                var request = {
                    requestShippingAddress: this.requestShippingAddress,
                    requestBillingInfo: this.requestBillingInfo,
                    currencyCode: this.currencyCode,
                    countryCode: shippingAddressData.countryId,
                    shippingContact: {
                        familyName: shippingAddressData.firstname,
                        givenName: shippingAddressData.lastname,
                        email: email,
                        country: shippingAddressData.countryId,
                        region: shippingAddressData.regionCode,
                        city: shippingAddressData.city,
                        addressLines: shippingAddressData.street,
                        postalCode: shippingAddressData.postcode
                    },
                    total: {
                        label: this.squareupMerchantName,
                        amount: grandTotal.toFixed(2).toString(),
                        pending: false
                    },
                    lineItems: this.getLineItems(quote.totals())
                };

                return request;
            },

            squarePayments: function() {
                const payments = Square.payments(this.SquareupApplicationId, this.SquareupLocationId);
                return payments;
            },

            squareCard: async function () {
                const payments = this.squarePayments();
                let postalCode = (!quote.billingAddress() || quote.billingAddress().postcode === null) ? '' : quote.billingAddress().postcode;
                this.card = await payments.card({
                "postalCode" : postalCode
                });
                await this.card.attach('#squareup_omni_card');

                const callback = async (postalCode) => {
                    if (typeof postalCode.detail.postalCodeValue !== 'undefined') {
                        this.postalCode(postalCode.detail.postalCodeValue);
                    }
                };

                this.card.addEventListener("postalCodeChanged", callback);
            },

            squareApplePay: async function() {
                if (this.isApplePayEnabled()) {
                    const payments = this.squarePayments();
                    const request = payments.paymentRequest(this.squarePaymentRequest());

                    try {
                        this.applePay = await payments.applePay(request);

                        // Apple pay is supported, display the button
                        const button = document.querySelector(
                            '.button-apple-pay'
                        ).style.display = 'block';
                    } catch (e) {
                        this.isApplePayEnabled(0);
                        return;
                    }
                }
            },

            squareGooglePay: async function() {
                if (this.isGooglePayEnabled()) {
                    try {
                        const payments = this.squarePayments();
                        const request = payments.paymentRequest(this.squarePaymentRequest());

                        this.googlePay = await payments.googlePay(request);
                        $('#squareup_omni_google_pay').html('');
                        await this.googlePay.attach('#squareup_omni_google_pay');
                    } catch (e) {
                        console.log(e);
                        return;
                    }
                }
            },

            giftcardTotal: function() {
                let gcTotal = 0;
                let allGiftCards = this.giftCards();

                allGiftCards.forEach(function (giftCard) {
                    gcTotal += giftCard.amount;
                });

                return gcTotal;
            },

            placeOrderClick: async function (item, event) {
                console.log('placeOrderClick');
                console.trace();
                let result = false;
                fullScreenLoader.startLoader();
                if (this.validate() && additionalValidators.validate()) {
                    result = await this.requestCardNonce();
                }
                fullScreenLoader.stopLoader();
                return result;
            },

            requestCardNonce: async function (item, event) {
                var existingNonce = document.getElementById('card-nonce');
                var _self = this;

                if (billing().isAddressSameAsShipping() && $('.onestep-shipping-address').is(':visible') && !customer.isLoggedIn()) {
                    var addressData = checkoutData.getShippingAddressFromData();
                    var add = createShippingAddress(addressData);
                    selectBillingAddressAction(add);

                    var newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddressAction(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                }

                // If gift cards are in use, submit order anyway
                if (this.isGiftCardUsed() &&
                    this.hasSubscriptionProducts() === false &&
                    this.grandTotal() === 0
                ) {
                    _self.displayForm(false);
                    _self.placeOrder();
                    return;
                }

                if (existingNonce.value.length === 0) {
                    await _self.handleSubmit();
                } else {
                    _self.placeOrder();
                }
            },

            handleSubmit: async function () {
                let method = jQuery("div.payment-option.credit-card._active").attr("method");
                this.messageContainer.clear();
                let result;

                try {
                    switch (method) {
                        case 'card':
                            result = await this.card.tokenize();
                            break;

                        case 'applePay':
                            result = await this.applePay.tokenize();
                            break;

                        case 'googlePay':
                            result = await this.googlePay.tokenize();
                            break;
                    }
                } catch (e) {
                    console.log('Catch on handleSubmit');
                    console.error(e);
                    return;
                }

                if (result.status === 'OK') {
                    // Assign the nonce value to the hidden form field
                    document.getElementById('card-nonce').value = result.token;
                    document.getElementById('card-brand').value = result.details.card.brand;
                    document.getElementById('card-last-4').value = result.details.card.last4;
                    document.getElementById('card-exp-month').value = result.details.card.expMonth;
                    document.getElementById('card-exp-year').value = result.details.card.expYear;


                    var billingAddressData = quote.billingAddress();
                    var email = (!quote.guestEmail) ? window.checkoutConfig.customerData.email : quote.guestEmail;
                    const payments = this.squarePayments();
                    var verifyBuyer = await payments.verifyBuyer(
                        result.token,
                        {
                            amount: quote.totals().grand_total.toString(),
                            intent: "CHARGE",
                            currencyCode: window.checkoutConfig.payment.squareup.squareupCurrencyCode,
                            billingContact: {
                                familyName: billingAddressData.firstname,
                                givenName: billingAddressData.lastname,
                                email: email,
                                country: billingAddressData.countryId,
                                city: billingAddressData.city,
                                addressLines: billingAddressData.street,
                                postalCode: billingAddressData.postcode,
                                phone: billingAddressData.telephone
                            }
                        }
                    );

                    if (verifyBuyer != 'undefined' &&
                        verifyBuyer.token != 'undefined' &&
                        verifyBuyer.token.length > 0
                    ) {
                        document.getElementById('buyerVerification-token').value = verifyBuyer.token;

                        this.verificationToken = verifyBuyer.token;
                        this.requestCardNonce();
                    } else {
                        alert('There was an error processing your payment');
                    }
                } else {
                    console.error(result);
                    alert('There was an error processing your payment');
                }
            },

            getData: function () {
                var data = this._super();
                var self = this;

                var cardNonce = $('#card-nonce').val();
                var ccType = $('#card-brand').val();
                var digitalWallet = $('#digital-wallet').val();
                var ccLast4 = $('#card-last-4').val();
                var ccExpMonth = $('#card-exp-month').val();
                var ccExpYear = $('#card-exp-year').val();
                if (cardNonce) {
                    data.additional_data.nonce = cardNonce;
                    data.additional_data.cc_type = ccType;
                    data.additional_data.digital_wallet = digitalWallet;
                    data.additional_data.cc_last_4 = ccLast4;
                    data.additional_data.cc_exp_month = ccExpMonth;
                    data.additional_data.cc_exp_year = ccExpYear;
                    data.additional_data.cc_postal_code = this.postalCode();
                }

                var buyerVerification = $('#buyerVerification-token').val();

                data.additional_data.buyerVerificationToken = this.verificationToken;
                data.additional_data.display_form = self.displayForm();

                var saveCard = $('#save-square-card').is(':checked');
                if (saveCard === true) {
                    data.additional_data.save_square_card = 1;
                }

                var saveCardHasValue = $('#save-square-card').val();
                if (saveCardHasValue == 1) {
                    data.additional_data.save_square_card = 1;
                }

                return data;
            },

            applyGiftCard: function () {
                squareGiftCard.applyGiftCard();
            },

            checkBalanceGiftCard: function () {
                squareGiftCard.checkBalanceGiftCard();
            },

            removeGiftCard: function (giftCard) {
                squareGiftCard.removeGiftCard(giftCard);
            },

            priceProcess: function (price) {
                price = parseFloat(price);

                if (isNaN(price) == false && price > 0) {
                    return priceUtils.formatPrice(price, quote.getPriceFormat());
                } else {
                    return '';
                }
            }
        });
    }
);
