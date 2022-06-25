define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Catalog/js/price-utils',
    'uiElement'
], function (_, utils, layout, priceUtils, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            tooltipTpl: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/base/tooltip',
            reports: {},
            reportColumnType: '',
            defaultReportColumnType: '',
            metricConfig: {
                name: '${ $.name }_metric',
                component: 'Aheadworks_AdvancedReports/js/ui/toolbar/bookmarks/dropdown',
                links: {},
                storageConfig: {
                    provider: 'ns = ${ $.ns }, index = bookmarks',
                    namespace: 'current.${ $.index }'
                },
                exports: {
                    applied: '${ $.name }:params.metric'
                }
            },
            imports: {
                priceFormat: '${ $.provider }:data.priceFormat',
                providerParams: '${ $.provider }:params',
                reports: '${ $.provider }:data.reports'
            },
            listens: {
                params: 'onMetricParamChange'
            },
            modules: {
                metric: '${ $.metricConfig.name }',
                parentComponent: '${ $.parentName }'
            }
        },

        /**
         * Initializes widget component
         *
         * @returns {Widget} Chainable
         */
        initialize: function () {
            this._super()
                .initReportColumnTypeByDefault()
                .initMetric();

            return this;
        },

        /**
         * Initializes observable properties
         *
         * @returns {Widget} Chainable
         */
        initObservable: function () {
            this._super()
                .track([
                    'reports',
                    'reportColumnType',
                    'priceFormat',
                    'providerParams'
                ]);

            return this;
        },

        /**
         * Initializes report column type by default
         *
         * @returns {Widget} Chainable
         */
        initReportColumnTypeByDefault: function () {
            this.set('reportColumnType', this.defaultReportColumnType);

            return this;
        },

        /**
         * Initializes change metric component
         *
         * @returns {Widget} Chainable
         */
        initMetric: function () {
            var metric = this.buildMetric();

            layout([metric]);

            return this;
        },

        /**
         * Configure metric selector component
         *
         * @returns {Object}
         */
        buildMetric: function () {
            var metric = {
                'options': this._getOptions(),
                'default': this.reportColumnType
            };

            metric = utils.extend({}, metric, this.metricConfig);

            return metric;
        },

        /**
         * Handles changes of 'metricParam' object
         *
         * @returns {Widget} Chainable
         */
        onMetricParamChange: function () {
            var metric = this.params['metric'];

            if (_.isEmpty(metric)) {
                this.initReportColumnTypeByDefault();
            } else {
                this.set('reportColumnType', metric);
            }

            return this;
        },

        /**
         * Convert value by column type
         *
         * @param {String} type
         * @param {String} value
         * @param {Boolean} addPrefix
         * @return {string}
         */
        convertByType: function (type, value, addPrefix) {
            var prefix = '',
                addPrefix = addPrefix || false;

            if (addPrefix && value > 0) {
                prefix = '+'
            }

            switch (type) {
                case 'percent':
                    value = !_.isUndefined(value) ? value : 0;
                    return prefix + String(Math.abs(Number(value * 1)).toFixed(2)) + '%';
                case 'price':
                    return prefix + priceUtils.formatPrice(value, this.priceFormat);
            }

            return prefix + String(Number(value * 1).toFixed(0));
        },


        /**
         * Get column option
         *
         * @return {Object}
         */
        getColumnOption: function () {
            var option = _.findWhere(this._getOptions(), {value: this.reportColumnType});

            return option;
        },

        /**
         * Retrieve column type
         *
         * @return {String}
         */
        getColumnType: function () {
            var option = this.getColumnOption();

            return option.columnType;
        },

        /**
         * Retrieve report type and column names
         *
         * @return {Array}
         */
        getReportData: function () {
            var reportData = this.reportColumnType.split('.');

            if (_.isArray(reportData) && _.size(reportData) === 2) {
                return reportData;
            }

            return [];
        },

        /**
         * Retrieve options
         *
         * @return {Array}
         */
        _getOptions: function () {
            return this.parentComponent().options;
        }
    });
});
