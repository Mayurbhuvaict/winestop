/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'jquery',
        'underscore',
        'ko',
        'uiComponent',
        'Mageplaza_GiftCard/js/model/product',
        'Magento_Catalog/js/price-utils',
        'mage/calendar',
        'mage/dataPost',
        'mage/translate'
    ],
    function ($, _, ko, Component, giftCard, priceUtils) {
        'use strict';

        var config        = window.giftCardInformation,
            configureData = window.configureData,
            productData   = config.information;

        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/product/view'
            },
            activeTemplate: giftCard.activeTemplate,
            activeImage: giftCard.activeImage,
            activeAmount: giftCard.activeAmount,
            activeDelivery: ko.observable(),
            deliveryFields: ko.observableArray(),
            allowOpenAmount: productData.hasOwnProperty('openAmount'),
            useRangeAmount: ko.observable(false),
            openAmountValue: ko.observable(),
            from: giftCard.from,
            to: giftCard.to,
            message: giftCard.message,
            maxLength: productData.messageMaxChar,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                this.initGiftCardData();
                this.initConfigureData();
                this.initWishlist();

                return this;
            },

            initWishlist: function () {
                $('a.action.tocompare, a.action.towishlist').each(function () {
                    var dataPost = $(this).data('post'),
                        formKey  = $('input[name="form_key"]').val();

                    if (formKey) {
                        dataPost.data.form_key = formKey;
                    }

                    $(this).bind('click', function (e) {
                        $.each($('#product_addtocart_form').serializeArray(), function (index, field) {
                            dataPost.data[field.name] = field.value;
                        });

                        $.mage.dataPost().postData(dataPost);

                        e.stopPropagation();
                        e.preventDefault();
                    });
                });
            },

            initEvent: function () {
                $('#giftcard-information-container-loader').remove();
            },

            /**
             * Init Observable
             */
            initObservable: function () {
                var self = this;

                this._super().observe({
                    deliveryDate: '',
                    timezone: productData.timezone.value,
                    remainChar: this.maxLength
                });

                this.productPriceEl = $('#product-price-' + productData.productId);
                this.activeAmount.subscribe(function (amount) {
                    self.productPriceEl.find('.price').text(amount.price);

                    $('[data-role=priceBox]').trigger('updatePrice');
                });

                if (this.allowOpenAmount) {
                    this.openAmountValue.subscribe(function (value) {
                        self.validateOpenAmount(parseFloat(value));
                    });
                }

                this.activeDelivery.subscribe(function (delivery) {
                    self.initDeliveryFields(delivery);
                });
                this.activeTemplate.subscribe(function (template) {
                    self.initDeliveryFields(self.activeDelivery(), template);
                });

                this.message.subscribe(function (value) {
                    if (value.length > self.maxLength) {
                        self.message(value.substring(0, self.maxLength));
                    } else {
                        self.remainChar(self.maxLength - value.length);
                    }
                });

                return this;
            },

            /**
             * Init Amount
             */
            initGiftCardData: function () {
                var self = this;

                this.amounts = [];
                $.each(productData.amounts, function (index, amount) {
                    var amountConverted = self.convertPrice(amount.amount);

                    self.amounts[index] = {
                        baseValue: amount.amount,
                        value: amountConverted,
                        amountFormatted: self.formatPrice(amountConverted),
                        price: self.convertAndFormat(amount.price)
                    };
                });

                if (this.allowOpenAmount) {
                    this.minAmount = this.convertPrice(productData.openAmount.min);
                    this.maxAmount = this.convertPrice(productData.openAmount.max);
                }

                if (this.amounts.length) {
                    this.activeAmount(this.amounts[0]);
                } else {
                    this.openAmountValue(this.minAmount);
                }

                this.deliveries = productData.delivery;
                this.activeDelivery(this.deliveries[0]);
            },

            initCalendar: function (type) {
                if (type === 'delivery_date') {
                    $('#delivery_date').calendar({
                        changeYear: false,
                        showWeek: false,
                        minDate: +0,
                        maxDate: "+1Y",
                        dateFormat: $.datepicker.W3C
                    });
                }
            },

            /**
             * Init saved data
             * @returns {exports}
             */
            initConfigureData: function () {
                var amount, delivery;

                if (configureData.hasOwnProperty('amount')) {
                    if (configureData.range_amount && this.allowOpenAmount) {
                        this.validateOpenAmount(configureData.amount);
                    } else {
                        amount = _.find(this.amounts, function (amountTmp) {
                            return amountTmp.baseValue === configureData.amount;
                        });

                        if (typeof amount !== 'undefined') {
                            this.changeAmount(amount);
                        }
                    }
                }
                if (configureData.hasOwnProperty('delivery')) {
                    delivery = _.find(this.deliveries, function (deliveryTmp) {
                        return deliveryTmp.key === Number(configureData.delivery);
                    });

                    if (typeof delivery !== 'undefined') {
                        this.activeDelivery(delivery);
                    }
                }
                if (configureData.hasOwnProperty('from')) {
                    this.from(configureData.from);
                }
                if (configureData.hasOwnProperty('to')) {
                    this.to(configureData.to);
                }
                if (configureData.hasOwnProperty('message') && configureData.message) {
                    this.message(configureData.message);
                }
                if (configureData.hasOwnProperty('delivery_date')) {
                    this.deliveryDate(configureData.delivery_date);
                }
                if (configureData.hasOwnProperty('timezone')) {
                    this.timezone(configureData.timezone);
                }
            },

            /**
             * Apply change for amounts dropdown
             * @param amount
             */
            changeAmount: function (amount) {
                this.activeAmount(amount);
                this.useRangeAmount(false);
            },

            /**
             * Check when click on open amount input
             * @returns {exports}
             */
            checkOpenAmount: function () {
                if (this.openAmountValue()) {
                    this.validateOpenAmount(this.openAmountValue());
                }

                return this;
            },

            /**
             * Apply change for input open amount
             */
            validateOpenAmount: function (openAmount) {
                var self = this;

                if (this.minAmount && openAmount < this.minAmount) {
                    openAmount = this.minAmount;
                }
                if (this.maxAmount && openAmount > this.maxAmount) {
                    openAmount = this.maxAmount;
                }

                if (openAmount > 0) {
                    this.openAmountValue(openAmount);
                    this.useRangeAmount(true);
                    this.activeAmount({
                        baseValue: self.convertPrice(openAmount, true),
                        value: openAmount,
                        amountFormatted: self.formatPrice(openAmount),
                        price: self.getPriceFromAmount(openAmount)
                    });
                } else {
                    this.openAmountValue('');
                }
            },

            /**
             * Get delivery fields
             * @returns {{}}
             */
            initDeliveryFields: function (activeDelivery, activeTemplate) {
                var deliveryFields = {},
                    templateFields = {};

                if (typeof activeTemplate === 'undefined') {
                    activeTemplate = this.activeTemplate();
                }

                if (typeof activeDelivery.fields !== 'undefined') {
                    $.each(activeDelivery.fields, function (index, field) {
                        if (activeDelivery.fields.hasOwnProperty(index)) {
                            deliveryFields[index] = field;
                        }
                    });
                }

                if (activeTemplate.hasOwnProperty('design')) {
                    templateFields = activeTemplate.design;
                }

                if (typeof templateFields.from !== 'undefined') {
                    deliveryFields.from = {
                        type: 'input',
                        label: $.mage.__('Sent From'),
                        name: 'from',
                        value: this.from,
                        placeHolder: $.mage.__('Sender name')
                    };
                }

                if (typeof templateFields.to !== 'undefined') {
                    deliveryFields.to = {
                        type: 'input',
                        label: $.mage.__('Sent To'),
                        name: 'to',
                        value: this.to,
                        placeHolder: $.mage.__('Recipient name')
                    };
                }

                if (typeof templateFields.message !== 'undefined') {
                    deliveryFields.message = {
                        type: 'textarea',
                        label: $.mage.__('Message'),
                        name: 'message',
                        value: this.message,
                        remainingLabel: $.mage.__('characters remaining')
                    };
                }

                if (productData.enableDeliveryDate) {
                    deliveryFields.deliveryDate = {
                        type: 'input',
                        label: $.mage.__('Delivery Date'),
                        name: 'delivery_date',
                        class: 'validate-date',
                        value: this.deliveryDate,
                        readonly: true
                    };
                }
                if (productData.timezone.enable) {
                    if (typeof this.timezoneList === 'undefined') {
                        this.timezoneList = $.map(productData.timezone.options, function (value) {
                            return [value];
                        });
                    }
                    deliveryFields.timezone = {
                        type: 'select',
                        label: $.mage.__('Timezone'),
                        name: 'timezone',
                        value: this.timezone,
                        options: this.timezoneList
                    };
                }

                this.deliveryFields($.map(deliveryFields, function (value) {
                    return [value];
                }));
            },

            /**
             * Get open amount price
             * @param amount
             * @returns {number}
             */
            getPriceFromAmount: function (amount) {
                return this.formatPrice(amount * productData.openAmount.rate / 100);
            },

            /**
             * Convert price to show
             * @param value
             * @param toBase
             * @returns {number}
             */
            convertPrice: function (value, toBase) {
                if (typeof toBase !== 'undefined') {
                    return parseFloat(value) / productData.currencyRate;
                }

                return parseFloat(value) * productData.currencyRate;
            },

            /**
             * Format Price
             * @param value
             * @returns {*|String}
             */
            formatPrice: function (value) {
                return priceUtils.formatPrice(value, productData.priceFormat);
            },

            /**
             * Convert and format
             * @param value
             * @returns {*|String}
             */
            convertAndFormat: function (value) {
                var convertValue = this.convertPrice(value);

                return this.formatPrice(convertValue);
            }
        });
    }
);

