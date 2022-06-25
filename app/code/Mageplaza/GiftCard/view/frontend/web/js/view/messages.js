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
        'ko',
        'Magento_Ui/js/view/messages',
        'Mageplaza_GiftCard/js/model/messageList'
    ],
    function (ko, Component, globalMessages) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/messages',
                selector: '[data-role=giftcard-messages]'
            },

            initialize: function (config, messageContainer) {
                this._super(config, messageContainer);

                this.messageContainer = globalMessages;

                return this;
            }
        });
    }
);

