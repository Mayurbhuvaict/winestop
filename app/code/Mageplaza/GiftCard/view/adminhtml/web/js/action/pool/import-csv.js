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
    'Mageplaza_GiftCard/js/action/pool/abstract'
], function ($, poolAction) {
    'use strict';

    return function (idPrefix, url) {
        var form = $('#edit_form'),
            file = $('#' + idPrefix + 'import_file'),
            data = new FormData(form[0]);

        data.append('import_file', file[0].files[0]);
        data.append('id', $('#pool_id').val());

        poolAction(idPrefix, url, data, true);
    };
});

