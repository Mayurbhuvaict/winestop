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
    'underscore'
], function ($, _) {
    'use strict';

    return function (idPrefix, url, payload, hasFile) {
        var ajaxOptions;

        var fieldset = $('#' + idPrefix + 'generate_fieldset');

        var validationResult = fieldset.find('input:visible, textarea:visible').map(function (index, elem) {
            return $.validator.validateElement(elem);
        });

        if (!_.every(validationResult)) {
            return;
        }

        if (fieldset.find('.messages')) {
            fieldset.find('.messages')[0].update();
        }

        ajaxOptions = {
            url: url,
            data: payload,
            method: 'post',
            showLoader: true,
            success: function (response) {
                if (!response) {
                    return;
                }

                if (response.success) {
                    window.poolCodesGridJsObject.reload();
                }

                if (fieldset.find('.messages')) {
                    fieldset.find('.messages')[0].update(response.messages);
                }
            }
        };

        if (hasFile) {
            ajaxOptions.processData = false;
            ajaxOptions.contentType = false;
        }

        $.ajax(ajaxOptions);
    };
});

