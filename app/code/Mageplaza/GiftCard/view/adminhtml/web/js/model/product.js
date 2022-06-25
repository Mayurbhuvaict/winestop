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

define(['jquery'], function ($) {
        "use strict";

        var hiddenFields = {
            template: "#template",
            image: "#image",
            amount: "#amount"
        };

        return {
            activeTemplate: [],
            activeImage: '',
            activeAmount: [],
            from: '',
            to: '',
            message: '',

            /**
             * set Product id
             *
             * @param {Number} id
             */
            setProductId: function (id) {
                this.productId = id;
            },

            /**
             * Set Amount
             *
             * @param {Object} amount
             */
            setAmount: function (amount) {
                if (typeof amount !== 'undefined') {
                    this.activeAmount = amount;
                    $('#product-price-' + this.productId).find(".price").text(amount.priceFormatted);
                    $(hiddenFields.amount).attr('price', amount.price).val(amount.baseValue).trigger("change");
                }
            },

            /**
             * Set template
             *
             * @param {Object} template
             */
            setTemplate: function (template) {
                this.activeTemplate = template;
                $(hiddenFields.template).val(template.id).trigger('change');
            },

            /**
             * Set image
             *
             * @param {String} image
             */
            setImage: function (image) {
                if (typeof image !== 'undefined') {
                    $('.image.item-template').removeClass('active');
                    $('.image-uploaded').removeClass('active');
                    this.activeImage = image;
                    $(hiddenFields.image).val(image).trigger('change');
                }
            },

            /**
             * Set delivery field value
             *
             * @param {String} fieldName
             * @param {string }value
             */
            setFieldValue: function (fieldName, value) {
                switch (fieldName) {
                    case 'from':
                        this.from = value;
                        break;
                    case 'to':
                        this.to = value;
                        break;
                    case 'message':
                        this.message = value;
                }
            }
        };
    }
);

