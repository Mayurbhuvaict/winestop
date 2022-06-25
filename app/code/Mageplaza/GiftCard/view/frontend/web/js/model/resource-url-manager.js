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
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, resourceUrlManager, quote) {
        "use strict";

        return $.extend(resourceUrlManager, {
            getApplyGiftCardUrl: function (code) {
                var params = this.getCheckoutMethod() === 'guest' ? {quoteId: quote.getQuoteId()} : {},
                    urls   = {
                        'guest': '/guest-carts/:quoteId/mpgiftcard/' + code,
                        'customer': '/carts/mine/mpgiftcard/' + code
                    };

                return this.getUrl(urls, params);
            },

            getApplyCreditUrl: function (amount) {
                var params = {},
                    urls   = {
                        'customer': '/carts/mine/mpgiftcredit/' + amount
                    };

                return this.getUrl(urls, params);
            }
        });
    }
);
