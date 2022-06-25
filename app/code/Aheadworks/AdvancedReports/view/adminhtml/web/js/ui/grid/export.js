define([
    'jquery',
    'Magento_Ui/js/grid/export'
], function ($, Export) {
    'use strict';

    return Export.extend({
        defaults: {
            imports: {
                params: '${ $.provider }:params',
                exportParams: '${ $.provider }:data.exportParams'
            }
        },

        /**
         * Retrieve params
         *
         * @returns {Object}
         */
        getParams: function () {
            if (Array.isArray(this.exportParams)) {
                if (this.exportParams.length) {
                    return $.extend(this.params, this.exportParams);
                }
            } else {
                if (Object.keys(this.exportParams).length) {
                    return $.extend(this.params, this.exportParams);
                }
            }
            return this.params;
        }
    });
});
