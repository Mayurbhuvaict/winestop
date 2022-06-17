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
        'mage/storage',
        'Mageplaza_GiftCard/js/model/dashboard',
        'Magento_Ui/js/model/messageList',
        'Mageplaza_GiftCard/js/model/messageList'
    ],
    function (storage, giftCard, messageList, giftCardMessageList) {
        'use strict';

        return function (deferred, code, isRemove) {
            if (typeof isRemove === 'undefined') {
                isRemove = false;
            }

            return storage.post(
                'mpgiftcard/index/addList',
                JSON.stringify({
                    code: code,
                    isRemove: isRemove
                })
            ).done(
                function (response) {
                    if (!response.errors) {
                        giftCard.giftCardLists(response.giftCardLists);

                        messageList.addSuccessMessage(response);
                        deferred.resolve();
                    } else {
                        messageList.addErrorMessage(response);
                        deferred.reject();
                    }

                    giftCardMessageList.clear();
                }
            ).fail(
                function () {
                    deferred.reject();
                }
            );
        };
    }
);

