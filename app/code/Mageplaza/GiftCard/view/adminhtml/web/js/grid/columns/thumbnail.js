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
    'Magento_Ui/js/grid/columns/thumbnail',
    'jquery',
    'mage/template',
    'text!Mageplaza_GiftCard/template/grid/cells/thumbnail/preview.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, thumbnailPreviewTemplate) {
    'use strict';

    return Column.extend({
        modal: {},
        preview: function (row) {
            var templateId, modalHtml;

            if (typeof row[this.index + '_design'] === 'undefined') {
                console.log('There is no preview for this template.');
                return;
            }

            templateId = row.template_id;
            if (typeof this.modal[templateId] === 'undefined') {
                modalHtml              = mageTemplate(
                    thumbnailPreviewTemplate,
                    {
                        card: row[this.index + '_card'],
                        design: row[this.index + '_design'],
                        images: row[this.index + '_images'],
                        alt: this.getAlt(row),
                        templateId: templateId
                    }
                );
                this.modal[templateId] = $('<div></div>')
                .html(modalHtml)
                .modal({
                    title: this.getAlt(row),
                    innerScroll: true,
                    modalClass: '_image-box',
                    buttons: []
                });
            }
            this.modal[templateId].trigger('openModal');
        }
    });
});

