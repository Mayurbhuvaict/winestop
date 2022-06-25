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
    'uiComponent',
    'Mageplaza_GiftCard/js/model/checkout',
    'Mageplaza_GiftCard/js/action/apply-gift-card',
    'Mageplaza_GiftCard/js/action/cancel-gift-card',
    'Mageplaza_GiftCard/js/action/apply-credit',
    'rjsResolver',
    'mage/translate',
    'mpIonRangeSlider'
], function ($, ko, Component, giftCard, applyCodeAction, cancelCodeAction, applyCreditAction, resolver, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_GiftCard/checkout/cart'
        },
        enableGiftCard: giftCard.enableGiftCard,
        enableGiftCredit: giftCard.enableGiftCredit,
        enableMultiple: giftCard.enableMultiple,

        giftCardCodeValue: giftCard.giftCardCodeValue,
        giftCardListValue: giftCard.giftCardListValue,
        giftCreditValue: giftCard.giftCreditValue,

        isLoading: giftCard.isLoading,
        isApplied: giftCard.isApplied,

        giftCardsUsed: giftCard.giftCardsUsed,
        listGiftCards: giftCard.listGiftCards,

        blockTitle: $t('Apply Gift Cards'),

        /**
         * Init component
         */
        initialize: function () {
            this._super();

            this.initFormData();

            resolver(this.afterResolveDocument.bind(this));
        },

        /**
         * Init form data
         * @returns {exports}
         */
        initFormData: function () {
            var self = this;

            if (!this.enableGiftCard()) {
                this.blockTitle = $t('Use Gift Credit');
            }

            this.enableGiftCardList = ko.computed(function () {
                return self.listGiftCards().length > 1;
            });

            return this;
        },

        /**
         * Credit slider from ion Range Slider
         */
        initCreditSlider: function () {
            var self          = this,
                sliderElement = $('#gift-card-credit-slider'),
                config;

            if (!sliderElement.length) {
                return this;
            }

            config = giftCard.config;

            sliderElement.ionRangeSlider({
                min: 0,
                max: config().maxUsed,
                from: config().creditUsed,
                from_shadow: true,
                step: 0.01,
                prettify: function (value) {
                    return giftCard.getFormattedPrice(value);
                },
                onStart: function (data) {
                    self.giftCreditValue(parseFloat(data.from).toFixed(2));
                },
                onChange: function (data) {
                    self.giftCreditValue(parseFloat(data.from).toFixed(2));
                },
                onFinish: function (data) {
                    if (this.secondLoad) {
                        applyCreditAction(data.from);
                    }

                    this.secondLoad = true;

                    return this;
                }
            });

            if (this.enableGiftCard()) {
                $('.gift-credit-apply').collapsible({
                    openedState: 'active',
                    saveState: false,
                    active: config().creditUsed > 0.0001
                });
            } else {
                $('.gift-credit-apply-content').show();
            }

            return sliderElement;
        },

        initSliderObservable: function () {
            var self          = this,
                sliderElement = $('#gift-card-credit-slider'),
                inputElement  = $('#gift-card-credit-input'),
                config        = giftCard.config;

            inputElement.change(function () {
                var value = parseFloat($(this).val()).toFixed(2);

                value = value > config().maxUsed ? config().maxUsed : value < 0 ? 0 : value;

                self.giftCreditValue(value);
                sliderElement.data("ionRangeSlider").update({from: value});

                applyCreditAction(value);
            });

            config.subscribe(function (value) {
                if (value.enableGiftCredit) {
                    if (!sliderElement.data("ionRangeSlider")) {
                        sliderElement = self.initCreditSlider();
                    }

                    self.giftCreditValue(value.creditUsed);
                    sliderElement.data("ionRangeSlider").update({
                        from: value.creditUsed,
                        max: value.maxUsed
                    });
                }
            });
        },

        afterResolveDocument: function () {
            this.initCreditSlider();
            this.initSliderObservable();
        },

        /**
         * Get used gift card label
         */
        getUsedGiftCardLabel: function (item) {
            var amountFormatted = giftCard.getFormattedPrice(parseFloat(item.amount));

            return item.code + ' (' + amountFormatted + ')';
        },

        /**
         * Apply Gift Card Code
         */
        applyGiftCardCode: function () {
            var form = $('#discount-giftcard-form');

            form.validation();

            if (form.valid()) {
                applyCodeAction(this.giftCardCodeValue());
            }
        },

        /**
         * Cancel Gift Card Code
         */
        cancelGiftCardCode: function (giftCardId) {
            if (!this.isApplied()) {
                return;
            }

            if (typeof giftCardId === 'undefined') {
                giftCardId = this.giftCardsUsed()[0].id;
            }

            cancelCodeAction(giftCardId);
        },

        /**
         * Apply Gift Card From Saved List
         */
        applySavedList: function () {
            if (this.giftCardListValue()) {
                applyCodeAction(this.giftCardListValue());
            }
        }
    });
});
