define([
    'mage/utils/strings',
    'underscore',
    'Aheadworks_AdvancedReports/js/ui/grid/columns/column'
], function (stringUtils, _, Column) {
    'use strict';

    return Column.extend({
        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @param {Object} row
         * @param {String} index
         * @returns {String}
         */
        getLabel: function (row, index) {
            var number;

            if (!_.isUndefined(index)) {
                number = row[index];
            } else {
                number = this._super(row);
            }

            if (stringUtils.isEmpty(number)) {
                return '0';
            } else if (Math.floor(number) == number) {
                return String(number * 1);
            }

            return String(Number(number * 1).toFixed(2));
        }
    });
});
