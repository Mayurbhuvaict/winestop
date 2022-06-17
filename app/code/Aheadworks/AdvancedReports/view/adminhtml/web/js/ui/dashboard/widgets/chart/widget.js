define([
    'jquery',
    'underscore',
    'mageUtils',
    'Aheadworks_AdvancedReports/js/chart/chart',
    'Aheadworks_AdvancedReports/js/ui/dashboard/widgets/numeric/widget'
], function ($, _, utils, googleChart, NumericWidget) {
    'use strict';

    return NumericWidget.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/chart/widget',
            chartOptions: {
                height: 250,
                width: '90%',
                pointSize: 5,
                lineWidth: 2,
                chartArea: {
                    bottom: 70
                },
                vAxes: {
                    0: {
                        format: '#',
                        title: ''
                    },
                    1: {
                        format: '#',
                        title: ''
                    }
                },
                hAxis: {
                    showTextEvery: 4
                },
                legend: {
                    textStyle: {
                        color: '#777'
                    }
                }
            },
            listens: {
                reports: 'drawChart',
                reportColumnType:  'drawChart',
                priceFormat: 'drawChart',
                providerParams: 'drawChart'
            },
            modules: {
                dateRangeFilter: '${ $.ns }.${ $.ns }.dashboard_toolbar.date_range_filter'
            }
        },

        /**
         * Initializes Listing component
         *
         * @returns {Widget} Chainable
         */
        initialize: function () {
            this._super()._bind();

            return this;
        },

        /**
         * Bind event
         */
        _bind: function () {
            _.bindAll(this, 'drawChart');
            $(window).on('resize', this.drawChart);
        },

        /**
         * Retrieve widget chart id
         *
         * @return {string}
         */
        getWidgetChartId: function () {
            return 'aw-arep__' + this.index;
        },

        /**
         * Retrieve widget chart template id
         *
         * @return {string}
         */
        getWidgetChartTemplateId: function () {
            return 'aw-arep__chart_tooltip_template_' + this.index;
        },

        /**
         * Is draw chart
         *
         * @returns {Boolean}
         */
        isDrawChart: function () {

            return this.reportColumnType
                && $('#' + this.getWidgetChartId())[0]
                && $('#' + this.getWidgetChartTemplateId())[0];
        },

        /**
         * Draw chart
         */
        drawChart: function () {
            var chart;

            if (this.isDrawChart()) {
                chart = this._initChart();
                chart.clearChart();
                chart.draw();
            }
        },

        /**
         * Init chart
         */
        _initChart: function () {
            var chart = googleChart({
                chartType: this.chartType,
                columns: this._getColumnsConfig(),
                priceFormat: this.priceFormat,
                serieDefaultOptions: this.serieDefaultOptions,
                rows: this.getItems(),
                clickOnChartSerieEnable: false,
                compareEnabled: true,
                chartContainerId: this.getWidgetChartId(),
                chartTooltipSelector: '#' + this.getWidgetChartTemplateId(),
                chartOptions: this.chartOptions
            });

            return chart;
        },

        /**
         * Retrieve columns config
         *
         * @return {Array}
         * @private
         */
        _getColumnsConfig: function () {
            var config = [
                {
                    visible: true,
                    displayOnChartAfterLoad: true,
                    label: 'Time Unit',
                    chartType: 'string',
                    index: 'xAxis',
                    color: '#111',
                    compareConfig: {
                        visibleInLegend: true
                    },
                    getLabel: function(row, index) {
                        if (!_.isUndefined(index)) {
                            return row[index];
                        }

                        return row['xAxis'];
                    }
                },
                {
                    visible: true,
                    displayOnChartAfterLoad: true,
                    label: this.dateRangeFilter().periodLabel,
                    chartType: 'number',
                    index: 'value',
                    color: '#487CEA',
                    compareConfig: {
                        color: '#00ae97',
                        label: this.dateRangeFilter().comparePeriodLabel
                    },
                    getLabel: function(row, index) {
                        if (!_.isUndefined(index)) {
                            return row['c_formattedValue'];
                        }

                        return row['formattedValue'];
                    }
                }
            ];

            return config;
        },

        /**
         * Retrieve chart items
         *
         * @return {Array}
         */
        getItems: function () {
            var items = [],
                preparedItems = [],
                reportData = this.getReportData(),
                reportType = reportData[0],
                reportColumn = reportData[1],
                reportChartConfig = this._getReportChartConfig();

            if (_.isObject(this.reports[reportType])) {
                items = this.reports[reportType]['chart']['rows'];
            }

            _.each(items, function (item) {
                var value = item[reportColumn],
                    cValue = item['c_' + reportColumn],
                    preparedItem = {
                        xAxis: item[reportChartConfig.xAxis],
                        c_xAxis: item['c_' + reportChartConfig.xAxis],
                        value: value,
                        c_value: cValue,
                        formattedValue: this.convertByType(this.getColumnType(), value),
                        c_formattedValue: this.convertByType(this.getColumnType(), cValue)
                    };

                preparedItems.push(preparedItem);
            }, this);

            return preparedItems;
        },

        /**
         * Retrieve report chart config
         *
         * @return {Object}
         */
        _getReportChartConfig: function () {
            var reportData = this.getReportData(),
                reportType = reportData[0],
                option = _.findWhere(this.parentComponent().options, {value: reportType});

            return option.chartConfig;
        }
    });
});
