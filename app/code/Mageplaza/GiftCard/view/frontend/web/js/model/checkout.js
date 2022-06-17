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

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($, ko, totals, quote, priceUtils, $t) {
    "use strict";

    var quoteData      = window.checkoutConfig.quoteData,
        giftCardConfig = ko.computed(function () {
            var extensionAttributes = totals.totals().extension_attributes || {};

            return JSON.parse(extensionAttributes.gift_cards || '{}');
        });

    var checkoutModel = {
        config: giftCardConfig,

        enableGiftCard: ko.computed(function () {
            return giftCardConfig().enableGiftCard;
        }),
        enableGiftCredit: ko.computed(function () {
            return giftCardConfig().enableGiftCredit;
        }),
        enableMultiple: ko.computed(function () {
            return giftCardConfig().enableMultiple;
        }),

        giftCardCodeValue: ko.observable(''),
        giftCardListValue: ko.observable(''),
        giftCreditValue: ko.observable(''),

        /**
         * List saved gift card of customer
         */
        listGiftCards: ko.observableArray([]),

        /**
         * Can show detail Gift Card code on order summary
         */
        canShowDetail: ko.computed(function () {
            return giftCardConfig().canShowDetail;
        }),

        /**
         * Block Gift Card is loading
         */
        isLoading: ko.observable(false),

        /**
         * List gift card is currently used on this quote
         */
        giftCardsUsed: ko.computed(function () {
            var giftCardsUsed       = [],
                giftCardsUsedConfig = giftCardConfig().giftCardUsed || {},
                id, amount;

            for (id in giftCardsUsedConfig){
                if (giftCardsUsedConfig.hasOwnProperty(id)) {
                    amount = parseFloat(giftCardsUsedConfig[id].amount) * -1;

                    giftCardsUsed.push({
                        id: id,
                        code: giftCardsUsedConfig[id].code,
                        amount: amount
                    });
                }
            }

            return giftCardsUsed;
        }),

        /**
         * Init started data
         * @returns {checkoutModel}
         */
        initialize: function () {
            var self = this, listGiftCard;

            this.isApplied = ko.computed(function () {
                return self.giftCardsUsed().length > 0;
            });

            this.checkAndDisplayGiftCodeInput();

            if (giftCardConfig().hasOwnProperty('listGiftCard')) {
                listGiftCard = $.map(giftCardConfig().listGiftCard, function (value) {
                    return [{code: value.code, label: value.hidden_code + ' (' + value.balance + ')'}];
                });
                listGiftCard.unshift({code: '', label: $t('-- Please Select --')});

                this.listGiftCards(listGiftCard);
            }

            return this;
        },

        /**
         * Get Segment Gift Card Data
         *
         * @returns {*}
         */
        getSegment: function (type) {
            if (typeof type === 'undefined') {
                type = 'gift_card';
            }

            return totals.getSegment(type);
        },

        /**
         * Check condition to display gift code on input field
         */
        checkAndDisplayGiftCodeInput: function () {
            if (!this.enableMultiple() && this.isApplied()) {
                this.giftCardCodeValue(this.giftCardsUsed()[0].code);
            } else {
                this.giftCardCodeValue('');
            }

            this.giftCardListValue('');
        },

        /**
         * Format price
         *
         * @param price
         * @returns {*|String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    };

    return checkoutModel.initialize();
});
