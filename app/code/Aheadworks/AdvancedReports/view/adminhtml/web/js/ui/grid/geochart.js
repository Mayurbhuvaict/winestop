define([
    'jquery',
    'underscore',
    'uiCollection',
    'mageUtils',
    'mage/template',
    'googleapi'
], function ($, _, Collection, utils, mageTemplate) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/geochart',
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            visible: {},
            statefull: {
                visible: true
            },
            imports: {
                totalColumnsCount: '${ $.columnsProvider }:initChildCount',
                addColumns: '${ $.columnsProvider }:elems',
                chartOptions: '${ $.provider }:data.chart.options',
                rows: '${ $.provider }:data.chart.rows',
                priceFormat: '${ $.provider }:data.priceFormat'
            },
            listens: {
                priceFormat: 'drawChart',
                elems: 'drawChart',
                rows: 'drawChart',
                chartOptions: 'drawChart',
                visible: 'updateVisible'
            },
            chartData: '',
            chart: '',
            chartContainerId: 'aw-arep__data_grid-geochart',
            chartLegendTemplateSelector: '#aw-chart-legend-template',
            chartLegendSelector: '#aw-arep__data_grid-geochart-legend',
            chartLegendElementSelector: '#aw-arep-chart-legend-',
            isDisplayLegend: true,
        },

        /**
         * Initializes Geochart component
         *
         * @returns {Geochart} Chainable
         */
        initialize: function () {
            _.bindAll(this, 'drawChart');

            this._super();

            google.charts.load('current', {'packages':['geochart']});
            google.charts.setOnLoadCallback(this.drawChart);

            $(window).on('resize', this.drawChart);
            return this;
        },

        /**
         * Adds columns whose visibility can be controlled to the component
         *
         * @param {Array} columns - Elements array that will be added to component
         * @returns {Columns} Chainable
         */
        addColumns: function (columns) {
            if (columns.length == this.totalColumnsCount) {
                var data = utils.copy(this.visible);

                columns = _.where(columns, {
                    visibleOnChart: true
                });

                this.insertChild(columns);

                var defaultColumn = {};
                var displayedColumnFound = false;
                this.elems().forEach(function (column) {
                    column.on('visible', this.drawChart);
                    if (this.visible[column.index] == undefined) {
                        data[column.index] = column.displayOnChartAfterLoad ? column.displayOnChartAfterLoad : false;
                    }
                    column.displayOnChartAfterLoad = data[column.index];
                    if (column.chartDefault) {
                        defaultColumn = column;
                    }
                    if (column.displayOnChartAfterLoad) {
                        displayedColumnFound = true;
                    }
                }, this);
                if (!displayedColumnFound) {
                    data[defaultColumn.index] = true;
                    this.elems().forEach(function (column) {
                        if (column.index == defaultColumn.index) {
                            column.displayOnChartAfterLoad = true;
                        }
                    }, this);
                }
                this.set('visible', utils.copy(data));
            }

            return this;
        },

        /**
         * Update displayOnChartAfterLoad for column from current bookmark
         *
         * @returns {Columns} Chainable
         */
        updateVisible: function () {
            if (this.visible == undefined) {
                this.visible = {};
            }
            this.elems().forEach(function (column) {
                if (this.visible[column.index] == undefined) {
                    this.visible[column.index] = column.displayOnChartAfterLoad ? column.displayOnChartAfterLoad : false;
                }
                column.displayOnChartAfterLoad = this.visible[column.index];
            }, this);
            this.drawChart();

            return this;
        },

        /**
         * Is draw chart
         *
         * @returns {Boolean}
         */
        canDrawChart: function () {
            if ((this.rows && this.rows.length > 0) && (this.elems() && this.elems().length > 0) && this.chartOptions) {
                return true;
            }
            return false;
        },

        /**
         * Initialization of geo chart
         *
         * @returns {Void}
         */
        initGeoChart: function () {
            this.chartData = new google.visualization.DataTable();
            this.chart = new google.visualization.GeoChart(document.getElementById(this.chartContainerId));
        },

        /**
         * Draw chart
         *
         * @returns {Void}
         */
        drawChart: function () {
            $('#' + this.chartContainerId).html('');
            $(this.chartLegendSelector).html('');
            if (!this.canDrawChart()) {
                return;
            }

            this.initGeoChart();

            var columns = this.getChartColumns();
            if (columns.chartColumn != undefined) {
                this.chartData.addColumn({'label': columns.countryColumn.label, 'type': columns.countryColumn.chartType});
                this.chartData.addColumn({'label': columns.chartColumn.label, 'type': columns.chartColumn.chartType});
                this.chartData.addRows(this.getChartRows());
            } else {
                // empty data
                this.chartData.addColumn({'label': 'Country', 'type': 'string'});
                this.chartData.addRows([['Empty']]);
            }

            var options = {
                height: 400,
                region: this.chartOptions.region,
                resolution: this.chartOptions.resolution,
            };

            if (!this.isDisplayLegend) {
                options.legend = 'none';
            }

            try {
                this.chart.draw(this.chartData, options);
            } catch (e) {}

            this.drawChartLegend();
        },

        /**
         * Retrieve chart columns
         *
         * @returns {Object}
         */
        getChartColumns: function() {
            var columns = {};
            this.elems().forEach(function (column) {
                if (column.chartType == 'string') {
                    columns.countryColumn = column;
                } else if (column.displayOnChartAfterLoad && column.visible) {
                    columns.chartColumn = column;
                }
            }, this);
            return columns;
        },

        /**
         * Retrieve chart rows
         *
         * @returns {Array}
         */
        getChartRows: function() {
            var self = this,
                chartRows = [],
                newRow = [],
                values = [];

            this.rows.forEach(function (row) {
                newRow = [];
                self.elems().forEach(function (column) {
                    if (column.chartType == 'number') {
                        if (column.displayOnChartAfterLoad && column.visible) {
                            values.push(parseFloat(row[column.index]));
                            newRow.push({'v': parseFloat(row[column.index]), 'f': column.getLabel(row)});
                        }
                    } else {
                        newRow.push({'v': column.getLabel(row), 'f': row['country']});
                    }
                });
                chartRows.push(newRow);
            });
            this.isDisplayLegend = !this.allTheSame(values);
            return chartRows;
        },

        /**
         * Check if all elements the same
         *
         * @param array
         * @returns {boolean}
         */
        allTheSame: function (array) {
            var first = array[0];
            return array.every(function(element) {
                return element === first;
            });
        },

        /**
         * Draw chart legend
         *
         * @returns {Void}
         */
        drawChartLegend: function() {
            var chartLegendEl = $(this.chartLegendSelector);
            chartLegendEl.hide();
            chartLegendEl.html('');
            this.elems().forEach(function (column) {
                if (column.chartType == 'number' && column.visible) {
                    chartLegendEl.append(this._getLegendContent(column.index, column.label, column.displayOnChartAfterLoad));
                    $(this.chartLegendElementSelector + column.index).on('click', this.clickOnLegend.bind(this));
                }
            }, this);
            chartLegendEl.show();
        },

        /**
         * Get legend content
         *
         * @param {String} index
         * @param {String} value
         * @param {Boolean} value
         * @returns {String}
         * @private
         */
        _getLegendContent: function(index, value, selected) {
            var legendTmpl = mageTemplate(this.chartLegendTemplateSelector);
            var chartLegendContent = legendTmpl({
                data: {
                    index: index,
                    value: value,
                    selected: selected,
                }
            });
            return chartLegendContent;
        },

        /**
         * Click on chart column
         *
         * @returns {void}
         */
        clickOnLegend: function(event) {
            var el = event.currentTarget;;
            var index = $(el).attr('data-index')

            if (index != undefined) {
                var data = utils.copy(this.visible);

                this.elems().forEach(function (column) {
                    if (column.chartType == 'number') {
                        if (index == column.index) {
                            column.displayOnChartAfterLoad = true;
                        } else {
                            column.displayOnChartAfterLoad = false;
                        }
                        data[column.index] = column.displayOnChartAfterLoad;
                    }
                }, this);

                this.set('visible', utils.copy(data));
            }
        },
    });
});
