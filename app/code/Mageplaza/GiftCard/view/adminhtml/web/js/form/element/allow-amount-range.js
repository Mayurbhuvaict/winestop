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

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            allowAmountRange: false,
            listens: {
                'allowAmountRange': 'toggleElement'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            var self = this, check;

            this._super().observe('allowAmountRange');

            check = setInterval(function () {
                var min_field  = $("input[name=\"product[min_amount]\"]"),
                    max_field  = $("input[name=\"product[max_amount]\"]"),
                    price_rate = $("input[name=\"product[price_rate]\"]");

                if (min_field.length && max_field.length && price_rate.length) {
                    self.disableField([min_field, max_field, price_rate]);
                    clearInterval(check);
                }
            }, 100);

            return this;
        },

        /**
         * Disable field
         *
         * @param {Array} fields
         */
        disableField: function (fields) {
            var self = this;

            $.each(fields, function (index, field) {
                field.prop('disabled', !self.allowAmountRange());
            });
        },

        /**
         * Toggle element
         */
        toggleElement: function () {
            this.disableField([
                $("input[name='product[min_amount]']"),
                $("input[name='product[max_amount]']"),
                $("input[name='product[price_rate]']")
            ]);
        }
    });
});

