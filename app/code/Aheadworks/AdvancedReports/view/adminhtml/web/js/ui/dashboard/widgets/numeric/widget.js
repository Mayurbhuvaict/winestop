define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Catalog/js/price-utils',
    'Aheadworks_AdvancedReports/js/ui/dashboard/widgets/abstract-widget'
], function (_, utils, layout, priceUtils, AbstractWidget) {
    'use strict';

    return AbstractWidget.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/numeric/widget',
            itemContainerTmpl: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/base/item-container',
        },

        /**
         * Retrieve additional difference classes
         *
         * @return {string}
         */
        getAdditionalDifferenceClasses: function () {
            var value = this.getDifferenceValue();

            if (value >= 0) {
                return 'increase';
            }

            return 'decrease';
        },

        /**
         * Retrieve total value
         *
         * @return {String}
         */
        getTotal: function () {
            var value = 0,
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1];

            if (_.isObject(this.reports[reportType])) {
                value = this.reports[reportType]['totals'][0][reportColumn];
            }

            return this.convertByType(this.getColumnType(), value);
        },

        /**
         * Retrieve difference value
         *
         * @return {Number}
         */
        getDifferenceValue: function () {
            var value = 0,
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1];

            if (_.isObject(this.reports[reportType])) {
                value = this.reports[reportType]['totals'][0]['diff_value_' + reportColumn];
            }

            return value;
        },

        /**
         * Retrieve formatted difference value
         *
         * @return {string}
         */
        getFormattedDifferenceValue: function () {
            var value = this.getDifferenceValue();

            return this.convertByType(this.getColumnType(), value, true);
        },

        /**
         * Retrieve difference value in percent
         *
         * @return {String}
         */
        getDifferenceValueInPercent: function () {
            var value = 0,
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1];

            if (_.isObject(this.reports[reportType])) {
                value = this.reports[reportType]['totals'][0]['diff_percent_' + reportColumn];
            }

            return this.convertByType('percent', value, false);
        },

        /**
         * Retrieve compare total value
         *
         * @return {String}
         */
        getCompareTotal: function () {
            var value = 0,
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1];

            if (_.isObject(this.reports[reportType])) {
                value = this.reports[reportType]['totals'][0]['c_' + reportColumn];
            }

            return this.convertByType(this.getColumnType(), value);
        },

        /**
         * Retrieve report url
         *
         * @return {String}
         */
        getReportUrl: function () {
            return this._mappingUrl(this._getDefaultReportUrl());
        },

        /**
         * Retrieve default report url
         *
         * @return {String}
         */
        _getDefaultReportUrl: function () {
            var reportData = this.getReportData(),
                reportType = reportData[0],
                option = _.findWhere(this._getOptions(), {value: reportType});

            return option.url;
        },

        /**
         * Mapping url
         *
         * @param {String} url
         * @return {String}
         */
        _mappingUrl: function (url) {
            _.each(this.providerParams, function (value, param) {
                if (_.isEmpty(value)) {
                    return;
                }

                url = this._mapUrlParam(url, value, param);
            }, this);

            return url;
        },

        /**
         * Map url param
         *
         * @param {String} url
         * @param {String} param
         * @param {String} value
         * @return {String}
         * @private
         */
        _mapUrlParam: function (url, value, param) {
            url = this._replaceUrlParam(url, param, value);

            return url;
        },

        /**
         * Replace url param
         *
         * @param {String} url
         * @param {String} param
         * @param {String} value
         * @return {String}
         */
        _replaceUrlParam: function (url, param, value) {
            var pattern = new RegExp('(\\?|\\&)(' + param + '=).*?(&|$)');

            if (url.search(pattern) >=0 ) {
                return url.replace(pattern, '$1$2' + value + '$3');
            }

            return url;
        }
    });
});
