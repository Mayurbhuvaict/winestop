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
    'Mageplaza_GiftCard/js/action/pool/import-csv',
    'Mageplaza_GiftCard/js/action/pool/manual-add',
    'Mageplaza_GiftCard/js/action/pool/generate'
], function ($, importCsv, manualAdd, generate) {
    'use strict';

    $.widget('mpgiftcard.pool', {
        _create: function () {
            $('#poolCodesGrid_massaction-select').addClass('ignore-validate');

            $('body').on('contentUpdated', '#poolCodesGrid', function () {
                $('#poolCodesGrid_massaction-select').addClass('ignore-validate');
            });

            window.mpImportPool   = importCsv;
            window.mpAddPool      = manualAdd;
            window.mpGeneratePool = generate;
        }
    });

    return $.mpgiftcard.pool;
});

