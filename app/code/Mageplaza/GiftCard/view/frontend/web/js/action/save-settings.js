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

        return function (loading, payload) {
            loading(true);

            return storage.post(
                'mpgiftcard/index/settings',
                JSON.stringify(payload)
            ).done(
                function (response) {
                    if (!response.errors) {
                        messageList.addSuccessMessage(response);
                    } else {
                        messageList.addErrorMessage(response);
                    }
                    giftCardMessageList.clear();
                }
            ).always(
                function () {
                    loading(false);
                }
            );
        };
    }
);

