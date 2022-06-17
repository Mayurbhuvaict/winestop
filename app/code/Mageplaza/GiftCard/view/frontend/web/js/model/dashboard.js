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
        'ko'
    ],
    function ($, ko) {
        "use strict";

        var giftCard = window.giftCard;

        return {
            baseUrl: giftCard.baseUrl,
            email: giftCard.customerEmail,
            code: giftCard.code,
            balance: ko.observable(giftCard.balance),
            transactions: ko.observableArray(giftCard.transactions),
            giftCardLists: ko.observableArray(giftCard.giftCardLists),
            notification: giftCard.notification,

            /** Is enable credit balance or not */
            isEnableCredit: function () {
                return giftCard.isEnableCredit;
            },

            /** Is enable setting fieldset */
            isEnableSetting: function () {
                return giftCard.notification.enable;
            }
        };
    }
);

