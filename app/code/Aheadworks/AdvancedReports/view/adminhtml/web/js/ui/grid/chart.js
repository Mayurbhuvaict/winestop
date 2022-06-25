define([
    'jquery',
    'underscore',
    'mageUtils',
    'Aheadworks_AdvancedReports/js/chart/chart',
    'uiCollection',
    'googleapi'
], function ($, _, utils, googleChart, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/chart',
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            visible: {},
            statefull: {
                visible: true
            },
            imports: {
                totalColumnsCount: '${ $.columnsProvider }:initChildCount',
                addColumns: '${ $.columnsProvider }:elems',
                rows: '${ $.provider }:data.chart.rows',
                compareEnabled: '${ $.provider }:data.compareEnabled',
                priceFormat: '${ $.provider }:data.priceFormat'
            },
            listens: {
                priceFormat: 'drawChart',
                elems: 'drawChart',
                rows: 'drawChart',
                visible: 'updateVisible'
            }
        },

        /**
         * Initializes Listing component
         *
         * @returns {Chart} Chainable
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
         * Update displayOnChartAfterLoad for column from current bookmark
         *
         * @returns {Chart} Chainable
         */
        updateVisible: function () {
            if (_.isUndefined(this.visible)) {
                this.visible = {};
            }
            this.elems().forEach(function (column) {
                if (_.isUndefined(this.visible[column.index])) {
                    this.visible[column.index] = column.displayOnChartAfterLoad
                        ? column.displayOnChartAfterLoad
                        : false;
                }
                column.displayOnChartAfterLoad = this.visible[column.index];
            }, this);
            this.drawChart();

            return this;
        },

        /**
         * Adds columns whose visibility can be controlled to the component
         *
         * @param {Array} columns - Elements array that will be added to component
         * @returns {Chart} Chainable
         */
        addColumns: function (columns) {
            if (columns.length == this.totalColumnsCount) {
                var data = utils.copy(this.visible);

                columns = _.where(columns, {
                    visibleOnChart: true
                });
                this.insertChild(columns);

                this.elems().forEach(function (column) {
                    column.on('visible', this.drawChart);
                    if (_.isUndefined(this.visible[column.index])) {
                        data[column.index] = column.displayOnChartAfterLoad
                            ? column.displayOnChartAfterLoad
                            : false;
                    }
                    column.displayOnChartAfterLoad = data[column.index];
                }, this);
                this.set('visible', utils.copy(data));
            }

            return this;
        },

        /**
         * Is draw chart
         *
         * @returns {Boolean}
         */
        isDrawChart: function () {
            if ((this.rows && this.rows.length > 1) && (this.elems() && this.elems().length > 1)) {
                return true;
            }
            return false;
        },

        /**
         * Draw chart
         */
        drawChart: function () {
            var yPos = window.scrollY,
                xPos = window.scrollX,
                chart = this._initChart();

            chart.clearChart();
            if (this.isDrawChart()) {
                chart.draw();
                window.scrollTo(xPos, yPos);
            }
        },

        /**
         * Init chart
         */
        _initChart: function () {
            var chart = googleChart({
                chartType: this.chartType,
                columns: this.elems(),
                priceFormat: this.priceFormat,
                serieDefaultOptions: this.serieDefaultOptions,
                rows: this.rows,
                compareEnabled: this.compareEnabled,
                chartOptions: this.chartOptions,
                beforeClickChartSeries: this.onBeforeClickChartSeries.bind(this),
                afterClickChartSeries: this.onAfterClickChartSeries.bind(this)
            });

            return chart;
        },

        /**
         * Before click on chart series
         *
         * @return {Object}
         */
        onBeforeClickChartSeries: function () {
            return utils.copy(this.visible);
        },

        /**
         * After click on chart series
         *
         * @return {Object}
         */
        onAfterClickChartSeries: function (columns, data) {
            this.set('visible', utils.copy(data));
        }
    });
});
