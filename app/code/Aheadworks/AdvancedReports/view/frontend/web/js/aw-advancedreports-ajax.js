define([
    'jquery'
], function($) {
    "use strict";

    $.widget('mage.awAdvancedReportsAjax', {
        options: {
            url: '/'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this.ajax();
        },

        /**
         * Send AJAX request
         */
        ajax: function () {
            var data = {};

            $.ajax({
                url: this.options.url,
                data: data,
                type: 'GET',
                cache: false,
                dataType: 'json',
                context: this,
            });
        },
    });

    return $.mage.awAdvancedReportsAjax;
});
