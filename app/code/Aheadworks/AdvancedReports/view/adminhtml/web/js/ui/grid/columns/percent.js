define([
    'mage/utils/strings',
    'underscore',
    'Aheadworks_AdvancedReports/js/ui/grid/columns/number'
], function (stringUtils, _, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReports/ui/grid/cells/percent'
        },

        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @param {Object} row
         * @param {String} index
         * @returns {String}
         */
        getLabel: function (row, index) {
            var number = this._super(row, index);

            return String(number) + '%';
        },

        /**
         * Return not formatted value of a current columns' field
         *
         * @param {Array} row
         * @param {String} index
         * @returns {String}
         */
        getValue: function (row, index) {
            var number;

            if (!_.isUndefined(index)) {
                number = row[index];
            } else {
                number = row[this.index];
            }

            if (stringUtils.isEmpty(number)) {
                number = '0';
            }

            return number;
        },

        /**
         * Retrieve width for percent bar
         *
         * @param {Array} row
         * @param {String} index
         * @returns {Integer}
         */
        getPercentBarWidth: function (row, index) {
            var width = Math.round(this.getValue(row, index) / 2);

            if (width > 50) {
                width = 50;
            }
            return width;
        },

        /**
         * Check is display percent bar
         *
         * @return {Boolean}
         */
        isDisplayPircentBar: function () {
            return this.displayPercentBar;
        }
    });
});