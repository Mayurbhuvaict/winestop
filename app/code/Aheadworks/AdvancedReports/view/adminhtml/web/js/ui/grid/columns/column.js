define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            headerTmpl: 'Aheadworks_AdvancedReports/ui/grid/columns/text',
            bodyTmpl: 'Aheadworks_AdvancedReports/ui/grid/cells/compare-text',
            imports: {
                compareEnabled: '${ $.provider }:data.compareEnabled'
            }
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .track([
                    'compareEnabled'
                ]);

            return this;
        },

        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @param {Object} row
         * @param {String} index
         * @returns {String}
         */
        getLabel: function (row, index) {
            if (!_.isUndefined(index)) {
                return row[index];
            }

            return this._super(row);
        },

        /**
         * Retrieve compare index
         *
         * @return {String}
         */
        getCompareIndex: function() {
            return 'c_' + this.index;
        },

        /**
         * Check if display compare label
         *
         * @param {Array} row
         * @return {boolean}
         */
        isDisplayTotalCompareValue: function () {
            return this.compareEnabled;
        },

        /**
         * Check if display compare label
         *
         * @param {Array} row
         * @return {boolean}
         */
        isDisplayCompareValue: function (row) {
            return ((!_.isUndefined(this.displayCompareValue) && this.displayCompareValue)
                    || _.isUndefined(this.displayCompareValue))
                && (_.isUndefined(row['display_compare']) || row['display_compare'])
                && this.compareEnabled;
        },

        /**
         * Retrieve additional difference classes
         *
         * @param {Array} row
         * @return {string}
         */
        getAdditionalDifferenceClasses: function (row) {
            var value = this.getDifferenceValue(row);

            if (value >= 0) {
                return 'increase';
            }

            return 'decrease';
        },

        /**
         * Retrieve difference value
         *
         * @param {Array} row
         * @return {Number}
         */
        getDifferenceValue: function (row) {
            var value = row['diff_value_' + this.index];

            return value || 0;
        },

        /**
         * Retrieve formatted difference value
         *
         * @param {Array} row
         * @return {string}
         */
        getFormattedDifferenceValue: function (row) {
            var value = this.getDifferenceValue(row);

            return this.getIncreaseDecreasePrefix(value) + this.getLabel(row, 'diff_value_' + this.index);
        },

        /**
         * Retrieve difference value in percent
         *
         * @param {Array} row
         * @return {String}
         */
        getDifferenceValueInPercent: function (row) {
            var value = !_.isUndefined(row['diff_percent_' + this.index]) ? row['diff_percent_' + this.index] : 0;

            return String(Math.abs(Number(value * 1)).toFixed(2)) + '%';
        },

        /**
         * Retrieve increase decrease prefix
         *
         * @param value
         * @return {string}
         */
        getIncreaseDecreasePrefix: function (value) {
            if (value > 0) {
                return '+';
            }

            return '';
        }
    });
});
