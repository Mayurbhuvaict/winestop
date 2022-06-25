define([
    'mage/translate',
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mageUtils'
], function ($t, $, alert, utils) {
    'use strict';

    return function (deferred, data) {
        $.ajax({
            url: data.saveUrl,
            type: "POST",
            data: utils.serialize(data.params),
            dataType: 'json',
            showLoader: true,

            /**
             * Success callback.
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            success: function (response) {
                if (response.error) {
                    alert({
                        content: response.error,
                    });
                    deferred.reject();
                } else {
                    deferred.resolve();
                }
            },

            /**
             * Error callback.
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            error: function (response) {
                alert({
                    title: $t('There has been an error'),
                    content: response.statusText,
                });
                deferred.resolve();
            }
        });
    };
});
