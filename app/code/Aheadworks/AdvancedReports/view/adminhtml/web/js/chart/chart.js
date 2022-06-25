define([
    'jquery',
    'underscore',
    'mage/translate',
    'mage/template',
    'mageUtils',
    'googleapi'
], function ($, _, $t, mageTemplate, utils) {
    'use strict';

    /**
     * Modal Window Widget
     */
    $.widget('mage.awArepChart', {
        options: {
            chartType: 'LineChart',
            columns: {},
            priceFormat: {},
            serieDefaultOptions: {},
            rows: [],
            clickOnChartSerieEnable: true,
            compareEnabled: false,
            chartContainerId: 'aw-arep-data-grid-chart',
            chartTooltipSelector: '#aw-chart-tooltip-template',
            chartOptions: {
                height: 400,
                pointSize: 8,
                lineWidth: 3,
                backgroundColor: 'transparent',
                vAxes: {
                    0: {
                        format: 'price',
                        title: $t('Sale Totals')
                    },
                    1: {
                        viewWindow: {
                            //max: 5
                        },
                        format: '#',
                        title: $t('Units')
                    }
                },
                vAxis: {
                    viewWindow: {
                        min: 0
                    },
                    textStyle: {
                        fontSize: 12,
                        color: '#777'
                    }
                },
                hAxis: {
                    textStyle: {
                        fontSize: 11,
                        color: '#777'
                    }
                },
                tooltip: {
                    textStyle: {
                        fontSize: 12
                    },
                    isHtml: true
                },
                legend: {
                    position: 'bottom',
                    maxLines: 5,
                    textStyle: {
                        fontSize: 13
                    }
                }
            },
            // Callback for before click chart series events:
            // beforeClickChartSeries: function () {},
            // Callback for after click chart series events:
            // afterClickChartSeries: function (columns, data) {},
        },
        chartData: '',
        chart: '',
        columnsCount: 3, // value, tooltip, style

        /**
         * Create widget
         */
        _create: function () {
            this._bind();
            google.charts.load('current', {'packages': ['corechart']});
        },

        /**
         * Bind event
         */
        _bind: function () {
            _.bindAll(this, 'draw', '_drawVisualization', '_clickOnChartSeries');
        },

        /**
         * Draw chart
         */
        draw: function () {
            google.charts.setOnLoadCallback(this._drawVisualization);
        },

        /**
         * Draw chart visualisation
         */
        _drawVisualization: function () {
            this._initChart();
            this._prepareChartColumns();

            this.chartData.addRows(this.getChartRows());
            this.chart.setOption('series', this._getSeriesForGoogleChart());
            this.chart.setView({columns: this._getColumnsForGoogleChart()});
            this.chart.draw();
        },

        /**
         * Clear chart
         */
        clearChart: function () {
            $('#' + this.options.chartContainerId).html('');
        },

        /**
         * Initialization of chart
         */
        _initChart: function () {
            this.chartData = new google.visualization.DataTable();
            this.chart = new google.visualization.ChartWrapper({
                chartType: this.options.chartType,
                containerId: this.options.chartContainerId,
                dataTable: this.chartData,
                options: this._prepareChartOptions()
            });

            if (this.options.clickOnChartSerieEnable) {
                google.visualization.events.addListener(this.chart, 'select', this._clickOnChartSeries);
            }
        },

        /**
         * Prepare chart columns visible
         */
        _prepareChartColumns: function () {
            _.each(this.options.columns, function (column, index) {
                this._prepareColumnSerie(column, index);
                this._prepareColumnVisible(column, index);
                this._addColumnData(column, index);
            }, this);
        },

        /**
         * Prepare column serie
         *
         * @param {Object} column
         * @param {Integer} index
         * @returns {Chart} Chainable
         */
        _prepareColumnSerie: function (column, index) {
            column.googleSerie = {};
            if (index > 0) {
                this.options.columns[index - 1].googleSerie = utils.extend(
                    {},
                    this.options.serieDefaultOptions,
                    column.chartSerieOptions
                );
            }
            return this;
        },

        /**
         * Prepare column visible
         *
         * @param {Object} column
         * @param {Integer} index
         * @returns {Chart} Chainable
         */
        _prepareColumnVisible: function (column, index) {
            if (index > 0) {
                this.options.columns[index - 1].googleSerie.visibleInLegend = column.visible;
            }
            if ((!column.displayOnChartAfterLoad || (column.displayOnChartAfterLoad && !column.visible))
                && index > 0
            ) {
                // Hide column
                column.googleColumn = ({
                    label: column.label,
                    type: column.chartType,
                    calc: function () {
                        return null;
                    }
                });
                // Coloring serie to gray and visible/hide in legend
                this.options.columns[index - 1].googleSerie.color = '#cccccc';
            } else {
                column.googleColumn = index;
            }
            return this;
        },

        /**
         * Add column configuration data
         *
         * @param {Object} column
         * @param {Integer} index
         * @returns {Chart} Chainable
         */
        _addColumnData: function (column, index) {
            if (this.options.compareEnabled && index > 0) {
                // compare col, tooltip col, style col
                this.chartData.addColumn({label: this._getCompareColumnLabel(column), type: column.chartType});
                this.chartData.addColumn({type: 'string', role: 'tooltip', p: {html: true}});
                this.chartData.addColumn({type: 'string', role: 'style'});
            }
            // base col
            this.chartData.addColumn({label: column.label, type: column.chartType});
            if (index > 0) {
                // tooltip col, style col
                this.chartData.addColumn({type: 'string', role: 'tooltip', p: {html: true}});
                this.chartData.addColumn({type: 'string', role: 'style'});
            }
            return this;
        },

        /**
         * Prepare chart options
         *
         * @returns {Object}
         */
        _prepareChartOptions: function () {
            this.options.chartOptions['colors'] = this._getColumnColors();
            this._prepareChartVerticalAxes();

            return this.options.chartOptions;
        },

        /**
         * Prepare chart vertical axes
         *
         * @returns {Chart} Chainable
         */
        _prepareChartVerticalAxes: function () {
            var pattern = this.options.priceFormat.pattern;

            if (_.isObject(this.options.chartOptions.vAxes)) {
                _.each(this.options.chartOptions.vAxes, function (value) {
                    if (_.isObject(value) && value.format === 'price' && pattern) {
                        value.format = pattern.replace('%s', '#');
                    }
                });
            }

            return this;
        },

        /**
         * Retrieve columns for google chart
         *
         * @returns {Array}
         */
        _getColumnsForGoogleChart: function() {
            var columns = [],
                columnIndex,
                i;

            _.each(this.options.columns, function (column, index) {
                if (this.options.compareEnabled && index > 0) {
                    columnIndex = columns.length;
                    for (i = 0; i < (this.columnsCount * 2); i++) {
                        // compare col, tooltip col, style col, base col, tooltip col ...
                        if (!_.isNumber(column.googleColumn) && (i == 0 || i == this.columnsCount)) {
                            columns.push(column.googleColumn);
                        } else {
                            columns.push(columnIndex + i);
                        }
                    }
                } else {
                    columnIndex = columns.length;
                    if (_.isNumber(column.googleColumn) && index > 0) {
                        columns.push(columnIndex); // base col
                    } else {
                        columns.push(column.googleColumn); // index col (X)
                    }
                    if (index > 0) {
                        // tooltip, style ...
                        for (i = 1; i < this.columnsCount; i++) {
                            columns.push(columnIndex + i);
                        }
                    }
                }
            }, this);
            return columns;
        },

        /**
         * Retrieve series for google chart
         *
         * @returns {Array}
         */
        _getSeriesForGoogleChart: function() {
            var series = [];

            _.each(this.options.columns, function (column) {
                if (this.options.compareEnabled) {
                    series.push(this._getCompareSerieConfig(column));
                }
                series.push(column.googleSerie);
            }, this);

            return series;
        },

        /**
         * Retrieve column colors
         *
         * @returns {Array}
         */
        _getColumnColors: function() {
            var colors = [];

            _.each(this.options.columns, function (column, index) {
                if (index > 0) {
                    if (this.options.compareEnabled) {
                        colors.push(this._getCompareColumnColor(column));
                    }
                    colors.push(column.color);
                }
            }, this);

            return colors;
        },

        /**
         * Retrieve compare column label
         *
         * @param {Object} column
         * @returns {String}
         */
        _getCompareColumnLabel: function(column) {
            if (_.isObject(column.compareConfig) && !_.isUndefined(column.compareConfig.label)) {
                return column.compareConfig.label;
            }

            return column.label;
        },

        /**
         * Retrieve compare column googleSerie config
         *
         * @param {Object} column
         * @returns {String}
         */
        _getCompareSerieConfig: function(column) {
            var compareSerie = utils.copy(column.googleSerie);

            if (_.isObject(column.compareConfig) && !_.isUndefined(column.compareConfig.visibleInLegend)) {
                compareSerie.visibleInLegend = column.visible;
            } else {
                compareSerie.visibleInLegend = false;
            }
            compareSerie.lineDashStyle = [2, 2];

            return compareSerie;
        },

        /**
         * Retrieve compare column color
         *
         * @param {Object} column
         * @returns {String}
         */
        _getCompareColumnColor: function(column) {
            if (_.isObject(column.compareConfig) && !_.isUndefined(column.compareConfig.color)) {
                return column.compareConfig.color;
            }

            return column.color;
        },

        /**
         * Retrieve chart rows
         *
         * @returns {Array}
         */
        getChartRows: function() {
            var chartRows = [],
                newRow;

            _.each(this.options.rows, function (row) {
                newRow = [];
                _.each(this.options.columns, function (column) {
                    if (column.chartType == 'number') {
                        if (this.options.compareEnabled) {
                            newRow = newRow.concat(this._getChartRow(column, true, row));
                        }
                        newRow = newRow.concat(this._getChartRow(column, false, row));
                    } else {
                        newRow.push(column.getLabel(row));
                    }
                }, this);
                chartRows.push(newRow);
            }, this);

            return chartRows;
        },

        /**
         * Retrieve chart row
         *
         * @param {Object} column
         * @param {Boolean} isCompare
         * @param {Array} row
         * @return {Array}
         */
        _getChartRow: function (column, isCompare, row) {
            var newRow = [],
                prefix = isCompare ? 'c_' : '',
                index = prefix + column.index,
                firstElementIndex = prefix + this.options.columns[0].index,
                tooltipLabel;

            // value
            newRow.push({
                'v': parseFloat(row[index]),
                'f': column.getLabel(row, index)
            });
            tooltipLabel = _.isEmpty(prefix) ? column.getLabel(row) : column.getLabel(row, index);
            // tooltip value
            newRow.push(this._getTooltipContent(
                row[firstElementIndex],
                column.label,
                tooltipLabel
            ));
            // style
            newRow.push(this._getColumnStyle(column, isCompare));

            return newRow;
        },

        /**
         * Retrieve column style
         *
         * @param {Object} column
         * @param {Boolean} isCompare
         * @return {String}
         */
        _getColumnStyle: function (column, isCompare) {
            var color = isCompare ? this._getCompareColumnColor(column) : column.color,
                opacity = isCompare ? 0.7 : 1;

            return 'color: ' + color + '; opacity: ' + opacity;
        },

        /**
         * Get tooltip content
         *
         * @param {String} period
         * @param {String} description
         * @param {String} value
         * @returns {String}
         * @private
         */
        _getTooltipContent: function(period, description, value) {
            var style = '',
                tooltipTmpl,
                tooltipContent;

            period = _.isString(period) ? period : '';
            description = _.isString(description) ? description : '';
            value = _.isString(value) ? value : '';

            if (period.length > 22 || (description.length + value.length) > 22) {
                style = '-wide';
            } else if (period.length <= 12 && (description.length + value.length) <= 12) {
                style = '-small';
            }

            tooltipTmpl = mageTemplate(this.options.chartTooltipSelector);
            tooltipContent = tooltipTmpl({
                data: {
                    style: style,
                    period: period,
                    description: description,
                    value: value
                }
            });

            return tooltipContent;
        },

        /**
         * Click on chart series
         *
         * @returns {void}
         */
        _clickOnChartSeries: function() {
            var sel = this.chart.getChart().getSelection();

            // If sel[0].row is null, then clicked on the legend
            if (sel.length < 1 || sel[0].row !== null) {
                return;
            }
            var index = sel[0].column,
                data = {};

            if (_.isFunction(this.options.beforeClickChartSeries)) {
                data = this.options.beforeClickChartSeries();
            }

            if (this.options.compareEnabled && index > 1) {
                // this.columnsCount * 2 = number of columns and columns for comparison
                index = Math.ceil(index / (this.columnsCount * 2));
            } else {
                index = Math.ceil(index / this.columnsCount);
            }

            // If hide column
            if (this.options.columns[index].googleColumn == index) {
                this.options.columns[index].displayOnChartAfterLoad = false;
            } else {
                this.options.columns[index].displayOnChartAfterLoad = true;
            }

            data[this.options.columns[index].index] = this.options.columns[index].displayOnChartAfterLoad;
            if (_.isFunction(this.options.afterClickChartSeries)) {
                this.options.afterClickChartSeries(this.options.columns, data);
            }
        }
    });

    return $.mage.awArepChart;
});
