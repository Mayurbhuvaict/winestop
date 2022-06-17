/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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
        'ko',
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Mageplaza_GiftCard/js/model/checkout',
        'mage/translate'
    ],
    function (ko, $, Component, giftCardModel, $t) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/totals/discount'
            },
            giftCardsUsed: giftCardModel.giftCardsUsed,

            /**
             * Is Gift Card Display
             */
            isDisplayed: ko.computed(function () {
                return !!giftCardModel.getSegment('gift_card');
            }),

            /**
             * Is Gift Credit Display
             */
            isCreditDisplayed: ko.computed(function () {
                return !!giftCardModel.getSegment('gift_credit');
            }),

            /**
             * Initial component
             */
            initialize: function () {
                var self = this;

                this._super();

                this.titleDisplay = ko.computed(function () {
                    if (giftCardModel.canShowDetail() && self.giftCardsUsed().length === 1) {
                        return self.getTitle() + ' (' + self.giftCardsUsed()[0].code + ')';
                    }

                    return self.getTitle();
                });

                this.ifShowDetails = ko.computed(function () {
                    return giftCardModel.canShowDetail() && self.giftCardsUsed().length > 1;
                });
            },

            /**
             * Gift Card Title
             * @returns {*}
             */
            getTitle: function () {
                var segment = giftCardModel.getSegment('gift_card');

                if (segment) {
                    return segment.title;
                }

                return $t('Gift Card');
            },

            /**
             * Credit title
             *
             * @returns {*}
             */
            creditTitle: function () {
                var segment = giftCardModel.getSegment('gift_credit');

                if (segment) {
                    return segment.title;
                }

                return $t('Gift Credit');
            },

            /**
             * get Value
             *
             * @returns {*|String}
             */
            getValue: function () {
                var discount = 0;

                $.each(giftCardModel.giftCardsUsed(), function(e, value) {
                    discount += value.amount;
                })

                return this.getFormattedPrice(discount);
            },

            /**
             * get Credit Value
             *
             * @returns {*|String}
             */
            getCreditValue: function () {
                return this.getFormattedPrice(giftCardModel.getSegment('gift_credit').value);
            }
        });
    }
);
