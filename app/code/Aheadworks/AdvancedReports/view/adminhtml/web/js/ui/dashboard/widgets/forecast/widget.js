define([
    'jquery',
    'underscore',
    'Aheadworks_AdvancedReports/js/ui/dashboard/widgets/abstract-widget'
], function ($, _, AbstractWidget) {
    'use strict';

    return AbstractWidget.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/forecast/widget'
        },

        /**
         * Retrieve forecast total value
         *
         * @return {String}
         */
        getForecastTotal: function () {
            var value = 0,
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1];

            if (_.isObject(this.reports[reportType])) {
                value = this.reports[reportType]['thisMonthForecast'][reportColumn];
            }

            return this.convertByType(this.getColumnType(), value);
        },

        /**
         * Retrieve options
         *
         * @return {Array}
         */
        _getOptions: function () {
            return this.options;
        }
    });
});
