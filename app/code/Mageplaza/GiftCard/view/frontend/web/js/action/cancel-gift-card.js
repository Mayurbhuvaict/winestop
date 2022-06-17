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
    'mage/storage',
    'Mageplaza_GiftCard/js/model/checkout',
    'Mageplaza_GiftCard/js/model/resource-url-manager',
    'Mageplaza_GiftCard/js/model/messageList',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/action/get-payment-information',
    'mage/translate'
], function (
    $,
    storage,
    giftCard,
    urlManager,
    messageContainer,
    totals,
    getTotalsAction,
    getPaymentInformationAction,
    $t
) {
    'use strict';

    return function (giftCardCode) {
        var url     = urlManager.getApplyGiftCardUrl(giftCardCode),
            message = $t('Remove gift card successfully.'),
            deferred;

        giftCard.isLoading(true);

        return storage.delete(url, false).done(function (response) {
            if (!response) {
                return;
            }

            deferred = $.Deferred();

            if ($('body').hasClass('checkout-cart-index')) {
                getTotalsAction([], deferred);
                $.when(deferred).done(function () {
                    giftCard.isLoading(false);
                    giftCard.checkAndDisplayGiftCodeInput();
                });
            } else {
                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    giftCard.isLoading(false);
                    totals.isLoading(false);
                    giftCard.checkAndDisplayGiftCodeInput();
                });
            }

            messageContainer.addSuccessMessage({'message': message});
        }).fail(function (response) {
            giftCard.isLoading(false);
            totals.isLoading(false);
            messageContainer.addErrorMessage(JSON.parse(response.responseText));
        }).always(function () {
            giftCard.isLoading(false);
            totals.isLoading(false);
        });
    };
});

