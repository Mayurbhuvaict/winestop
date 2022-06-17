define([
    'underscore',
    'uiCollection'
], function (_, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/totals',
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            imports: {
                addColumns: '${ $.columnsProvider }:elems',
                totals: '${ $.provider }:data.totals'
            },
            listens: {
                elems: 'updateVisible'
            }
        },

        /**
         * Initializes Totals component
         *
         * @returns {Totals} Chainable
         */
        initialize: function () {
            _.bindAll(this, 'updateVisible');

            this._super();

            return this;
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Totals} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    totals: [],
                    visibleColumns: []
                });

            return this;
        },

        /**
         * Adds columns whose visibility can be controlled to the component
         *
         * @param {Array} columns - Elements array that will be added to component
         * @returns {Totals} Chainable
         */
        addColumns: function (columns) {
            columns = _.where(columns, {
                topTotalsVisible: true
            });

            this.insertChild(columns);

            return this;
        },

        /**
         * Called when another element was added to current component
         *
         * @returns {Totals} Chainable
         */
        initElement: function (element) {
            element.on('visible', this.updateVisible);

            return this._super();
        },

        /**
         * Check if at least one column is visible
         *
         * @return {Boolean}
         */
        atLeastOneColumnIsVisible: function () {
            return this.visibleColumns.length;
        },

        /**
         * Updates array of visible columns
         *
         * @returns {Totals} Chainable
         */
        updateVisible: function () {
            this.visibleColumns = _.where(this.elems(), {
                visible: true
            });

            return this;
        },

        /**
         * Gets current top totals label
         *
         * @param {Object} col
         * @returns {String}
         */
        getTotalLabel: function(col) {
            if ('topTotalsLabel' in col) {
                return col.topTotalsLabel;
            } else {
                return col.label;
            }
        }
    });
});
