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
    'Magento_Catalog/js/price-utils',
    'Magento_Sales/order/create/form',
    'mpIonRangeSlider'
], function ($, priceUtils) {
    "use strict";

    var initCreditSlider = function () {
        var sliderElement = $('#gift-card-credit-slider'), creditData;

        if (sliderElement.length) {
            creditData = sliderElement.data('credit');
            sliderElement.ionRangeSlider({
                min: 0,
                max: creditData.maxUsed,
                from: creditData.creditUsed,
                from_shadow: true,
                step: 0.01,
                prettify: function (value) {
                    return priceUtils.formatPrice(value, creditData.priceFormat);
                },
                onFinish: function (data) {
                    if (this.secondLoad) {
                        window.order.creditAmount(data.from);
                    }

                    this.secondLoad = true;
                    return this;
                }
            });
        }

        $('.gc-remove').click(function () {
            window.order.cancelGiftCode($(this).data('code'));
        });
    };

    window.order.applyGiftCode = function (code) {
        this.loadArea(['totals', 'billing_method', 'items'], true, {
            'gc_apply_code': code
        }).done(initCreditSlider);
    };

    window.order.cancelGiftCode = function (code) {
        this.loadArea(['totals', 'billing_method', 'items'], true, {
            'gc_cancel_code': code
        }).done(initCreditSlider);
    };

    window.order.creditAmount = function (amount) {
        this.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {
            'gc_apply_credit': amount
        }).done(initCreditSlider);
    };

    return initCreditSlider;
});
