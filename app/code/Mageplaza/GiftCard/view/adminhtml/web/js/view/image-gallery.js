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
    'jquery',
    'productGallery'
], function ($, productGallery) {
    'use strict';

    $.widget('mage.productGallery', productGallery, {
        options: {
            types: {}
        },

        _create: function () {
            this._super();
        },

        setBase: function () {
            return this;
        },

        _updateImagesRoles: function () {
            return this;
        }
    });

    return $.mage.productGallery;
});

